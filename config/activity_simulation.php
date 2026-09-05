<?php

return [
    'timezone' => 'Asia/Tehran',

    // Algorithm/config version used later by the seed (Phase 2). Not the admin values.
    'configuration_version' => 'v1',

    // Seed secret. Falls back to APP_KEY so no host .env edit is required.
    'secret' => env('ACTIVITY_SIMULATION_SECRET', config('app.key')),

    // Time Anchors (control points), NOT fixed buckets — the engine (Phase 2)
    // builds a smooth 24h curve through them.
    'anchor_times' => [
        'midnight' => '02:00',
        'morning'  => '09:00',
        'noon'     => '13:30',
        'evening'  => '17:30',
        'night'    => '21:30',
    ],

    // Sensible first-load defaults matching the spec examples; used by the admin
    // page when no DB value exists yet.
    'defaults' => [
        'online'    => ['midnight' => 20, 'morning' => 40, 'noon' => 75, 'evening' => 130, 'night' => 220],
        'watching'  => ['midnight' => 5, 'morning' => 12, 'noon' => 22, 'evening' => 38, 'night' => 65],
        'tolerance' => 20,
    ],

    // The Setting keys this feature reads/writes (single source of truth for key names).
    'setting_keys' => [
        'online' => [
            'midnight' => 'activity_online_midnight',
            'morning'  => 'activity_online_morning',
            'noon'     => 'activity_online_noon',
            'evening'  => 'activity_online_evening',
            'night'    => 'activity_online_night',
        ],
        'watching' => [
            'midnight' => 'activity_watching_midnight',
            'morning'  => 'activity_watching_morning',
            'noon'     => 'activity_watching_noon',
            'evening'  => 'activity_watching_evening',
            'night'    => 'activity_watching_night',
        ],
        'tolerance' => 'activity_tolerance',
    ],
];
