<?php

return [
    /**
     * Africa's Talking API credentials
     * Obtain these from https://africastalking.com
     */
    'at_api_key'   => env('AFRICAS_TALKING_API_KEY', ''),
    'at_username'  => env('AFRICAS_TALKING_USERNAME', ''),
    'at_sender_id' => env('AFRICAS_TALKING_SENDER_ID', 'RECOVERIES'),
];
