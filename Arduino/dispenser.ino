#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WebServer.h>

// Pin Ultrasonik, Relay, dan LED
#define TRIG_PIN D5     
#define ECHO_PIN D6     
#define RELAY_PIN D7    
#define LED_RED_PIN D3  
#define LED_YELLOW_PIN D4 

// WiFi Credentials
const char* ssid = "iPhone";            
const char* password = "soleh030604";   

ESP8266WebServer server(80);  

// Server URL untuk update status pompa
const String serverUrl = "http://172.20.10.2/dispenser/public/api/update-status";  

WiFiClient client;  
long duration;      
int distance;       
bool pumpActive = false;  

void setup() {
  Serial.begin(115200);  

  // Konfigurasi pin
  pinMode(TRIG_PIN, OUTPUT);  
  pinMode(ECHO_PIN, INPUT);   
  pinMode(RELAY_PIN, OUTPUT); 
  pinMode(LED_RED_PIN, OUTPUT);  
  pinMode(LED_YELLOW_PIN, OUTPUT);  
  digitalWrite(RELAY_PIN, HIGH);  
  digitalWrite(LED_RED_PIN, LOW);  
  digitalWrite(LED_YELLOW_PIN, LOW);  

  // Sambungkan ke WiFi
  Serial.println("Menghubungkan ke WiFi...");
  WiFi.begin(ssid, password);  
  int attempts = 0;  
  while (WiFi.status() != WL_CONNECTED && attempts < 20) {
    delay(1000);  
    Serial.print(".");  
    attempts++;
  }
  
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nTerhubung ke WiFi! IP: " + WiFi.localIP().toString());  
  } else {
    Serial.println("\nGagal terhubung ke WiFi.");  
  }

  // Rute untuk mengontrol suhu melalui tombol Laravel
  server.on("/set-temperature", []() {
    if (server.hasArg("status")) {
      String status = server.arg("status");
      Serial.println("Status suhu diterima: " + status);

      if (status == "Panas") {
        setTemperatureLED("Panas");
        server.send(200, "text/plain", "LED Merah Nyala");
      } else if (status == "Dingin") {
        setTemperatureLED("Dingin");
        server.send(200, "text/plain", "LED Kuning Nyala");
      } else if (status == "Normal") {
        setTemperatureLED("Normal");
        server.send(200, "text/plain", "Semua LED Mati");
      } else {
        server.send(400, "text/plain", "Status tidak valid");
      }
    } else {
      server.send(400, "text/plain", "Parameter status tidak ditemukan");
    }
  });

  server.begin();
  Serial.println("HTTP server dimulai.");
}

void loop() {
  server.handleClient(); 

  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("Menghubungkan ulang ke WiFi...");
    WiFi.begin(ssid, password);
  }

  measureDistance();  
  controlPump();      
  delay(1000);        
}

// Fungsi untuk mengukur jarak menggunakan sensor ultrasonik
void measureDistance() {
  digitalWrite(TRIG_PIN, LOW);  
  delayMicroseconds(2);         
  digitalWrite(TRIG_PIN, HIGH); 
  delayMicroseconds(10);        
  digitalWrite(TRIG_PIN, LOW);  

  duration = pulseIn(ECHO_PIN, HIGH, 30000);  
  if (duration == 0) {
    Serial.println("Sensor error: Tidak ada echo terdeteksi.");  
    distance = -1;  
  } else {
    distance = duration * 0.034 / 2;  
    Serial.println("Jarak terdeteksi: " + String(distance) + " cm");  
  }
}

// Fungsi untuk mengontrol pompa berdasarkan jarak
void controlPump() {
  if (distance > 0 && distance <= 15) {
    if (distance <= 10 && pumpActive) {
      pumpActive = false;  
      digitalWrite(RELAY_PIN, HIGH);  
      Serial.println("Ketinggian air 10 cm. Pompa OFF.");  
      sendPumpStatusToServer("OFF", distance);  
    } 
    else if (distance > 10 && !pumpActive) {
      pumpActive = true;  
      digitalWrite(RELAY_PIN, LOW);  
      Serial.println("Objek terdeteksi. Pompa ON.");  
      sendPumpStatusToServer("ON", distance);  
    }
  } 
  else if (distance > 15 || distance == -1) {
    if (pumpActive) {
      pumpActive = false;  
      digitalWrite(RELAY_PIN, HIGH);  
      Serial.println("Tidak ada objek. Pompa OFF.");  
      sendPumpStatusToServer("OFF", distance);  
    }
  }
}

// Fungsi untuk mengirimkan status pompa ke server
void sendPumpStatusToServer(String pumpStatus, int objectDistance) {
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;  
    String fullUrl = serverUrl + "?pumpStatus=" + pumpStatus + "&distance=" + String(objectDistance);  

    Serial.println("Mengirim data ke URL: " + fullUrl);  
    http.begin(client, fullUrl);  
    int httpCode = http.GET();  

    if (httpCode > 0) {
      Serial.println("Respons server: " + String(httpCode));  
    } else {
      Serial.println("Gagal mengirim data ke server.");  
    }
    http.end();  
  } else {
    Serial.println("WiFi tidak terhubung. Data tidak dapat dikirim.");  
  }
}

// Fungsi untuk mengatur LED berdasarkan suhu
void setTemperatureLED(String temperature) {
  if (temperature == "Panas") {
    digitalWrite(LED_RED_PIN, HIGH);  
    digitalWrite(LED_YELLOW_PIN, LOW);  
    Serial.println("LED Merah: ON, LED Kuning: OFF");
  } else if (temperature == "Dingin") {
    digitalWrite(LED_RED_PIN, LOW);   
    digitalWrite(LED_YELLOW_PIN, HIGH);  
    Serial.println("LED Merah: OFF, LED Kuning: ON");
  } else {
    digitalWrite(LED_RED_PIN, LOW);   
    digitalWrite(LED_YELLOW_PIN, LOW);  
    Serial.println("Semua LED mati.");
  }
}
