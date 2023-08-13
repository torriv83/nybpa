<?php

namespace App\Filament\Resources\UtstyrResource\Pages;

use App\Filament\Resources\UtstyrResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUtstyr extends ViewRecord
{
    protected static string $resource = UtstyrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
