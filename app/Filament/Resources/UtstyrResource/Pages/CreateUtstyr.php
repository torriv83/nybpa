<?php

namespace App\Filament\Resources\UtstyrResource\Pages;

use App\Filament\Resources\UtstyrResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUtstyr extends CreateRecord
{
    protected static string $resource = UtstyrResource::class;

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }
}
