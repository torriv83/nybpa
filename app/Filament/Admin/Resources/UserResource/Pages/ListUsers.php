<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Mail\SendMessageMail;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->color('warning'),
            Action::make('Send melding til ansatte')
                ->form([
                    Select::make('assistent')->multiple()
                        ->options(User::all()->filter(fn ($value) => $value->id != Auth::User()->id)->pluck('name',
                            'email')),
                    MarkdownEditor::make('body')->required(),
                ])
                ->action(function (array $data) {
                    Mail::to('tor@trivera.net')
                        ->bcc($data['assistent'])
                        ->send(clone new SendMessageMail(
                            body: $data['body'],
                        ));
                }),
        ];
    }
}
