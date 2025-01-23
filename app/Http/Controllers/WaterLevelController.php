<?php

namespace App\Http\Controllers;

use App\Events\PumpStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WaterLevelController extends Controller
{
    /**
     * Update status pompa berdasarkan data dari ESP8266.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'pumpStatus' => 'required|string',
            'distance' => 'required|numeric',
        ]);
    
        $pumpStatus = $request->pumpStatus;
        $distance = $request->distance;
    
        Cache::put('pump_status', [
            'pumpStatus' => $pumpStatus,
            'distance' => $distance,
        ], now()->addMinutes(10));
    
        event(new PumpStatusUpdated($pumpStatus, $distance));
        Log::info('Pump Status Updated', ['status' => $pumpStatus, 'distance' => $distance]);
    
        return response()->json([
            'message' => 'Pump status updated successfully.',
            'data' => Cache::get('pump_status'),
        ]);
    } 
    

    /**
     * Tampilkan status pompa di view.
     */
    public function showPumpStatus()
    {
        // Ambil data status pompa dari cache
        $status = Cache::get('pump_status', [
            'pumpStatus' => 'OFF',
            'distance' => 0,
        ]);

        // Data suhu fiktif
        $temperature = rand(15, 35); // Simulasi suhu antara 15°C hingga 35°C

        return view('pump-status', compact('status', 'temperature'));
    }

    /**
     * Set temperature.
     */
    public function setTemperature(Request $request)
    {
        $temperature = $request->temperature;

        // Simpan suhu ke cache
        Cache::put('temperature', $temperature, now()->addMinutes(10));

        return redirect('/pump-status');
    }

    /**
     * Toggle pump status.
     */
    public function togglePump()
    {
        $status = Cache::get('pump_status', [
            'pumpStatus' => 'OFF',
            'distance' => 0,
        ]);
    
        // Toggle status pompa
        $newStatus = ($status['pumpStatus'] === 'ON') ? 'OFF' : 'ON';
    
        // Simpan status pompa di cache
        Cache::put('pump_status', [
            'pumpStatus' => $newStatus,
            'distance' => $status['distance'],
        ], now()->addMinutes(10));
    
        return redirect('/pump-status');
    }
    
}
