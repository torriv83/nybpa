<?php

namespace App\Filament\Pages;

use App\Settings\BpaTimer;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\TextInput;

class BPA extends SettingsPage
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $settings = BpaTimer::class;

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('timer')
                ->label('Antall timer i uka')
                ->required(),
        ];
    }
}
