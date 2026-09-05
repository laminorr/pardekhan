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

    // ─────────────────────────────────────────────────────────────────────
    // Engine tunables (Phase 2). ALL knobs of the synthetic time-series engine
    // live here — no magic numbers in code. `shared` is the baseline for both
    // metrics; `online` / `watching` override it (deep-merged) so that «online»
    // swings a bit more and «watching» stays calmer and lower. Amplitudes are
    // expressed as a ratio of the admin `tolerance` value, so retuning the
    // natural band automatically rescales every stochastic layer.
    // ─────────────────────────────────────────────────────────────────────
    'engine' => [
        'shared' => [
            // Scales the admin `tolerance` into this metric's effective band.
            'tolerance_scale' => 1.0,

            // One drift factor per day: baseline is multiplied by (1 ± drift).
            'daily_drift' => ['min' => 0.03, 'max' => 0.06],

            // Sum of seeded sines. Periods are jittered per day so the wave
            // never repeats; total amplitude = amp_tolerance_ratio * tolerance.
            'slow_wave' => [
                'periods_minutes'     => [47, 83, 137],
                'amp_tolerance_ratio' => 0.28,
                'period_jitter_ratio' => 0.10,
            ],

            // AR(1) mean-reverting noise (correlated, NOT white).
            // n_t = phi*n_{t-1} + sigma*gauss ; sigma = ratio * tolerance.
            'noise' => ['ar1_phi' => 0.85, 'sigma_tolerance_ratio' => 0.10],

            // Slow calm/rising/falling bias that persists then reverts.
            'regime' => [
                'switch_prob_per_min'  => 0.004,
                'bias_tolerance_ratio' => 0.15,
                'reversion_per_min'    => 0.03, // how fast applied bias tracks/decays
            ],

            // Real flat holds (nothing changes for a few minutes).
            'pause' => ['prob_per_min' => 0.06, 'max_hold_minutes' => 4],

            // Rare small additive jump.
            'micro_burst' => ['prob_per_min' => 0.0015, 'size_tolerance_ratio' => 0.4],

            // Minute-to-minute change cap = ratio * tolerance.
            'rate_limit' => ['max_delta_tolerance_ratio' => 0.25],

            // Cross-midnight continuity: both the end of day D and the start of
            // day D+1 are pulled toward a shared, date-seeded boundary value so
            // there is no jump at the 00:00 rollover.
            'boundary' => ['drift_ratio' => 0.02, 'ramp_minutes' => 15],

            'min_floor' => 5,
        ],

        // «online» swings a little more than the shared defaults.
        'online' => [
            'slow_wave' => ['amp_tolerance_ratio' => 0.32],
            'regime'    => ['bias_tolerance_ratio' => 0.18],
        ],

        // «watching» is calmer, lower and less bursty.
        'watching' => [
            'tolerance_scale' => 0.6,
            'slow_wave'       => ['amp_tolerance_ratio' => 0.18],
            'noise'           => ['sigma_tolerance_ratio' => 0.07],
            'regime'          => ['bias_tolerance_ratio' => 0.10],
            'micro_burst'     => ['prob_per_min' => 0.0008],
            'min_floor'       => 2,
        ],

        // Final sanity guard (applied ONLY in the manager, never per-engine):
        // «watching» must stay meaningfully below «online».
        'ratio_guard' => ['watching_max_fraction_of_online' => 0.5],
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
