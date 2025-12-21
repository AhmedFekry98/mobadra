<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Stripe Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Stripe payment processing
    |
    */

    'secret_key' => env('STRIPE_SECRET_KEY'),
    'publishable_key' => env('STRIPE_PUBLISHABLE_KEY'),
    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    
    /*
    |--------------------------------------------------------------------------
    | Stripe API Version
    |--------------------------------------------------------------------------
    |
    | The Stripe API version to use
    |
    */
    'api_version' => '2023-10-16',

    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency for payments
    |
    */
    'currency' => env('STRIPE_CURRENCY', 'usd'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Stripe webhook handling
    |
    */
    'webhook' => [
        'tolerance' => 300, // 5 minutes tolerance for webhook timestamps
        'events' => [
            'payment_intent.succeeded',
            'payment_intent.payment_failed',
            'payment_intent.canceled',
            'payment_intent.requires_action',
            'checkout.session.completed',
            'checkout.session.expired',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for payment processing
    |
    */
    'payment' => [
        'confirmation_method' => 'automatic',
        'capture_method' => 'automatic',
        'setup_future_usage' => null, // Set to 'off_session' if you want to save payment methods
    ],

    /*
    |--------------------------------------------------------------------------
    | Checkout Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Stripe Checkout sessions
    |
    */
    'checkout' => [
        'success_url' => env('FRONTEND_URL', env('APP_URL')) . '/payment/success',
        'cancel_url' => env('FRONTEND_URL', env('APP_URL')) . '/payment/cancel',
    ],
];
