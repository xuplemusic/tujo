<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Clarifai API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for interacting with the Clarifai API.
    | You will need to obtain a Personal Access Token (PAT) and specify the
    | ID of the model you wish to use for face verification.
    |
    */

    'pat' => env('CLARIFAI_PAT'),
    'model_id' => env('CLARIFAI_MODEL_ID', 'face-detection'), // A default, but user should verify
    'user_id' => env('CLARIFAI_USER_ID'), // The user ID from your Clarifai account
    'app_id' => env('CLARIFAI_APP_ID'), // The App ID from your Clarifai application
];
