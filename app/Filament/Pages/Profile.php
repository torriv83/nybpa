<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Phpsa\FilamentAuthentication\Pages\Profile as PagesProfile;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
// use Filament\Forms\Concerns\InteractsWithForms;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Profile extends PagesProfile
{

    public function mount(): void
    {
        $this->form->fill([
            'name' => $this->getFormModel()->name,
            'email' => $this->getFormModel()->email,
            'phone' => $this->getFormModel()->phone,
            'adresse' => $this->getFormModel()->adresse,
            'postnummer' => $this->getFormModel()->postnummer,
            'poststed' => $this->getFormModel()->poststed,
        ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $state = array_filter([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'adresse' => $data['adresse'],
            'postnummer' => $data['postnummer'],
            'poststed' => $data['poststed'],
            'password' => $data['new_password'] ? Hash::make($data['new_password']) : null,
        ]);

        $this->getFormModel()->update($state);

        if ($data['new_password']) {
            // @phpstan-ignore-next-line
            Filament::auth()->login($this->getFormModel(), (bool) $this->getFormModel()->getRememberToken());
        }

        $this->notify('success', strval(__('filament::resources/pages/edit-record.messages.saved')));
    }

    public function getCancelButtonUrlProperty(): string
    {
        return static::getUrl();
    }

    protected function getBreadcrumbs(): array
    {
        return [
            url()->current() => 'Profile',
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Generelt')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('email')
                        ->label('Email Address')
                        ->required(),
                    Textinput::make('phone')
                        ->label('Telefon')
                        ->required(),
                    TextInput::make('adresse'),
                    TextInput::make('postnummer'),
                    TextInput::make('poststed'),
                    TextInput::make('assistentnummer')->disabled(),
                    TextInput::make('ansatt_dato')->disabled(),


                ]),
            Section::make('Oppdater Passord')
                ->columns(2)
                ->schema([
                    TextInput::make('current_password')
                        ->label('Current Password')
                        ->password()
                        ->rules(['required_with:new_password'])
                        ->currentPassword()
                        ->autocomplete('off')
                        ->columnSpan(1),
                    TextInput::make('new_password')
                        ->label('New Password')
                        ->rules(['confirmed', Password::defaults()])
                        ->autocomplete('new-password'),
                    TextInput::make('new_password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->rules([
                            'required_with:new_password',
                        ])
                        ->autocomplete('new-password'),
                ]),
        ];
    }
}