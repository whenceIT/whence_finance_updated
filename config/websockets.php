<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard Authentication
    |--------------------------------------------------------------------------
    |
    | The dashboard is only available for users that pass the authentication
    | check. Through this configuration you can toggle if the dashboard
    | is active and which route it resolves to.
    |
    */
    'dashboard' => [
        'enabled' => false,
        'path' => 'laravel-websockets',
        'middleware' => ['web', 'auth'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Statistics
    |--------------------------------------------------------------------------
    |
    | Collect statistics about connected websocket clients. Disabled by default.
    |
    */
    'statistics' => [
        'enabled' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | The request origin will be checked against these patterns. You can use
    | the asterisk (*) character to match any subdomain or port.
    |
    */
    'allowed_origins' => [
        'ws://127.0.0.1:6001',
        'http://127.0.0.1:6001',
        'http://localhost:6001',
        'ws://localhost:6001',
        '*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Maximum Message Size
    |--------------------------------------------------------------------------
    |
    | The maximum allowed message size in bytes. Messages larger than this
    | will be rejected.
    |
    */
    'max_message_size' => 10240000,

    /*
    |--------------------------------------------------------------------------
    | Applications
    |--------------------------------------------------------------------------
    |
    | This array contains the settings for each application. Each application
    | has its own ID and secret key that are used to authenticate requests.
    |
    */
    'apps' => [
        [
            'id' => 'slms',
            'key' => 'local',
            'secret' => 'local',
            'name' => 'Smart Events',
            'path' => '/app',
            'capacity' => null,
            'enable_client_messages' => true,
            'enable_statistics' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Settings
    |--------------------------------------------------------------------------
    */
    'server' => [
        'linger' => 1,
    ],
];
