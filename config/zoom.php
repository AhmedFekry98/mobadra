<?php

return [
    'client_id' => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'account_id' => env('ZOOM_ACCOUNT_ID'),
    'base_url' => env('ZOOM_BASE_URL', 'https://api.zoom.us/v2'),
    'oauth_url' => env('ZOOM_OAUTH_URL', 'https://zoom.us/oauth/token'),
    'webhook_secret_token' => env('ZOOM_WEBHOOK_SECRET_TOKEN'),
];
