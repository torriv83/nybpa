<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;
use STS\FilamentImpersonate\Pages\Actions\Impersonate;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => Cache::tags(['bruker'])->flush()),
            Impersonate::make()->record($this->getRecord())->redirectTo(route('filament.assistent.pages.dashboard')),
        ];
    }

    protected function afterSave(): void
    {
        Cache::tags(['bruker'])->flush();
    }
}
