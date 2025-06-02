<?php

return [
    // Define the scheduled tasks
    'tasks' => [
        // Check wantlist availability daily at 8:00 AM
        [
            'command' => 'wantlist:check-availability',
            'frequency' => 'daily',
            'at' => '08:00',
            'environments' => ['production'],
        ]
    ]
];
