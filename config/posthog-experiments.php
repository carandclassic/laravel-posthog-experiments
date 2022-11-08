<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PostHog API Key
    |--------------------------------------------------------------------------
    |
    | This value is the API key that is supplied to you from PostHog.
    | You should be able to find this under the Project Seettings
    | https://app.posthog.com/project/settings
    |
    */
    'key' => env('POSTHOG_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | PostHog Cookie Session Name
    |--------------------------------------------------------------------------
    |
    | This value is the fallback unique identifier
    | That is used when a user is not logged in.
    |
    */
    'cookie_key' => env('POSTHOG_COOKIE_KEY', 'laravel_session'),

    /*
    |--------------------------------------------------------------------------
    | PostHog Domain
    |--------------------------------------------------------------------------
    |
    | This value is the PostHog domain that
    | Should be used for the API calls.
    |
    */
    'domain' => env('POSTHOG_DOMAIN', 'https://app.posthog.com'),
];
