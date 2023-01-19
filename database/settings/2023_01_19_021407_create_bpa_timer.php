<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateBpaTimer extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('bpa.timer', 7);
    }
}
