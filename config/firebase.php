<?php
return [
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS'),
    ],

    'project_id' => env('FIREBASE_PROJECT_ID'),
    'auth_token_verification_enabled' => true,
];
