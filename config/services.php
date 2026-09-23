<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Gateway for Android (sms-gate.app)
    |--------------------------------------------------------------------------
    |
    | Texts are sent by the phone sitting in the hotel, through its own SIM,
    | so there is no SMS bill and no third party involved.
    |
    | mode = local   the phone runs a small server on the same Wi-Fi as this
    |                site. Only works when both are on that network.
    | mode = cloud   messages go through https://api.sms-gate.app and the
    |                phone collects them. This is the one to use on the live
    |                server, which can never reach a phone at the hotel.
    |
    | The username and password are the ones shown inside the phone app.
    |
    */

    'sms_gateway' => [
        'mode' => env('SMS_GATEWAY_MODE', 'cloud'),
        'url' => env('SMS_GATEWAY_URL'),
        'username' => env('SMS_GATEWAY_USERNAME'),
        'password' => env('SMS_GATEWAY_PASSWORD'),
        'daily_limit' => (int) env('SMS_DAILY_LIMIT', 95),

        // The SMS pages ask for a short code as well as an admin login.
        // Only the one way hash is kept here, never the code itself.
        // To change the code, run:
        //   php artisan tinker --execute="echo password_hash('YOURCODE', PASSWORD_BCRYPT);"
        // and put the result in SMS_ACCESS_CODE_HASH in .env
        'access_code_hash' => env('SMS_ACCESS_CODE_HASH', '$2y$10$fBvvoQqnCK8VRKJhxxEmpurJJ/LSCsrw5quzLGhXErxsOvDRZNbR6'),
    ],

];
