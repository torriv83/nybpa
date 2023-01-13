<?php

return [

    'title' => 'Login',

    'heading' => 'Logg inn på din konto',

    'buttons' => [

        'submit' => [
            'label' => 'Logg inn',
        ],

    ],

    'fields' => [

        'email' => [
            'label' => 'E-post addresse',
        ],

        'password' => [
            'label' => 'Passord',
        ],

        'remember' => [
            'label' => 'Husk meg',
        ],

    ],

    'messages' => [
        'failed' => 'Brukernavn eller passord er feil.',
        'throttled' => 'For mange påloggingsforsøk. Prøv igjen om :seconds sekunder.',
    ],

];
