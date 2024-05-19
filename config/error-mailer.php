<?php

return [
    'email' => [
        'recipient' => ['webmaster@riveraconsulting.no'],
        'bcc' => [],
        'cc' => [],
        'subject' => 'An error was occured - ' . env('APP_NAME'),
    ],

    'disabledOn' => [
        //
    ],

    'cacheCooldown' => 10, // in minutes
];
