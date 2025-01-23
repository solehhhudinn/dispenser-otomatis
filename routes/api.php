<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

// Rute untuk memperbarui status pompa
Route::get('/api/update-status', function (Request $request) {
    $validated = $request->validate([
        'pumpStatus' => 'required|string|in:ON,OFF',
        'distance' => 'required|numeric|min:0',
    ]);

    try {
        // Update status pompa di cache selama 10 menit
        Cache::put('pump_status', $validated, now()->addMinutes(10));

        Log::info('Pump status updated', $validated);

        return response()->json([
            'message' => 'Status updated successfully',
            'data' => $validated
        ], 200);
    } catch (\Exception $e) {
        Log::error('Failed to update pump status', ['error' => $e->getMessage()]);
        return response()->json([
            'message' => 'Failed to update status',
            'error' => $e->getMessage()
        ], 500);
    }
});

// Rute untuk mendapatkan status pompa
Route::get('/api/get-status', function () {
    $status = Cache::get('pump_status', [
        'pumpStatus' => 'OFF',
        'distance' => 0,
    ]);

    return response()->json($status);
});

// Rute alternatif untuk mendapatkan status pompa (opsional, jika diperlukan)
Route::get('/get-status', function () {
    $status = Cache::get('pump_status', [
        'pumpStatus' => 'OFF',
        'distance' => 0,
    ]);

    return response()->json($status);
});

// Rute untuk memperbarui status suhu (LED) dengan metode POST
Route::post('/dispenser/change-temperature', function (Request $request) {
    $request->validate([
        'status' => 'required|string|in:Panas,Normal,Dingin'
    ]);

    $esp_ip = '172.20.10.6';  // Sesuaikan dengan IP ESP8266 Anda
    $url = "http://$esp_ip/set-temperature?status=" . $request->input('status');

    try {
        $response = Http::get($url);
        return response()->json(['response' => $response->body()]);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal menghubungi ESP8266', 'message' => $e->getMessage()], 500);
    }
});
