<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\Log;

class PumpStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public string $pumpStatus;
    public float $distance;

    public function __construct(string $pumpStatus, float $distance)
    {
        $this->pumpStatus = $pumpStatus;
        $this->distance = $distance;
    
        Log::info('Event PumpStatusUpdated dikirim', [
            'pumpStatus' => $pumpStatus,
            'distance' => $distance,
        ]);
    }

    public function broadcastOn(): Channel
    {
        return new Channel('pump-status');
    }
}
