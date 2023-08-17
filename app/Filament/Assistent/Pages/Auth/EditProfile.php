<?php

namespace App\Filament\Assistent\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                TextInput::make('phone')->label('Telefon'),
                TextInput::make('adresse'),
                TextInput::make('postnummer'),
                TextInput::make('poststed'),
                $this->getPasswordFormComponent(),

                $this->getPasswordConfirmationFormComponent(),
            ]);
    }
}
