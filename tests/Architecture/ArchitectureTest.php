<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

arch()->preset()->laravel()->ignoring(['App\Mail\SendReminderWhenPrescriptionExpire',
    'App\Mail\SendMessageMail',
    'App\Mail\BestillUtstyr',
    'App\Mail\TimesheetReminderEmail',
    'App\Mail\WorkReminderMail',
    'App\Providers\Filament']);
// 'App\Providers\Filament',
