<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'bulk_sms' => [
        'url' => env('BULK_SMS_URL', 'https://mshastra.com/sendsms_api_json.aspx'),
        'user' => env('BULK_SMS_USER'),
        'password' => env('BULK_SMS_PASSWORD'),
        'sender' => env('BULK_SMS_SENDER', 'WHENCELTD'),
        'language' => env('BULK_SMS_LANGUAGE', 'English'),
    ],

];
