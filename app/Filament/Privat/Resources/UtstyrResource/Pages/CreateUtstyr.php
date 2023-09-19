<?php

namespace App\Filament\Privat\Resources\UtstyrResource\Pages;

use App\Filament\Privat\Resources\UtstyrResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUtstyr extends CreateRecord
{
    protected static string $resource = UtstyrResource::class;

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }
}
