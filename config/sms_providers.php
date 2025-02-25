<?php

return [
    'twilio' => [
        'auth_token'    => env("TWILIO_AUTH_TOKEN"),
        'account_sid'   => env("TWILIO_ACCOUNT_SID"),
        'twilio_number' => env("TWILIO_NUMBER"),
    ],
];
