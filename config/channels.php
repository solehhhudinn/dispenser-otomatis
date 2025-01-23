<?php

use App\Events\PumpStatusUpdated;

return [
    'channels' => [
        'pump-status' => PumpStatusUpdated::class,
    ],
];
