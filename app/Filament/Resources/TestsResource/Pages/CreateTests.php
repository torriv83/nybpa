<?php

namespace App\Filament\Resources\TestsResource\Pages;

use App\Filament\Resources\TestsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTests extends CreateRecord
{
    protected static string $resource = TestsResource::class;

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }
}
