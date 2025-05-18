<?php

namespace App\Filament\Assistent\Resources\TimesheetResource\Pages;

use App\Filament\Assistent\Resources\TimesheetResource;
use App\Models\User;
use App\Transformers\FormDataTransformer;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTimesheet extends CreateRecord
{
    protected static string $resource = TimesheetResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return FormDataTransformer::transformFormDataForSave($data);

    }

    protected function afterCreate(): void
    {
        $recipient = User::query()->role('admin')->get();

        Notification::make()
            ->title(auth()->user()->name.' Har lagt til en tid han/hun ikke kan jobbe.')
            ->actions([
                Action::make('se')
                    ->url(route('filament.admin.resources.timelister.index', [
                        'tableFilters' => [
                            'Ikke tilgjengelig' => [
                                'isActive' => true,
                            ],
                            'assistent' => [
                                'value' => auth()->user()->id,
                            ],
                        ],
                    ]))
                    ->button(),
            ])
            ->sendToDatabase($recipient);
    }
}
