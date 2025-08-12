<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Biometric Verification Service
    |--------------------------------------------------------------------------
    |
    | This is the base URL for the external biometric verification service.
    | The application will send face and fingerprint data to this endpoint
    | for processing and verification.
    |
    */

    'api_url' => env('BIOMETRIC_API_URL'),
];
