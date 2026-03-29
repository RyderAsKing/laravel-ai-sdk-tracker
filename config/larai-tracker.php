<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard Password
    |--------------------------------------------------------------------------
    |
    | A single password to protect the Larai Tracker dashboard.
    | Set via ENV: LARAI_TRACKER_PASSWORD=your_secret
    | Or override from the Settings page (stored in DB, takes highest priority).
    |
    | When null and not in "local" environment, access is denied until a
    | password is configured.
    |
    */

    'password' => env('LARAI_TRACKER_PASSWORD', null),

    /*
    |--------------------------------------------------------------------------
    | Session Lifetime (minutes)
    |--------------------------------------------------------------------------
    |
    | How long the authenticated session lasts before requiring re-login.
    |
    */

    'session_lifetime' => 120,

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Tracking
    |--------------------------------------------------------------------------
    |
    | Toggle interception of Illuminate HTTP client responses.
    |
    | When Laravel AI SDK is installed, this defaults to false so usage is
    | tracked once per logical AI invocation through AI SDK events instead of
    | once per underlying provider HTTP call.
    |
    */

    'track_http_client' => env('LARAI_TRACKER_TRACK_HTTP_CLIENT', ! class_exists(\Laravel\Ai\Events\AgentPrompted::class)),
];
