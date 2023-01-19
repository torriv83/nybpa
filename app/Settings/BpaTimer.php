<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class BpaTimer extends Settings
{

    public $timer;

    public static function group(): string
    {
        return 'bpa';
    }
}
