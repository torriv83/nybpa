<?php
/**
 * Created by ${USER}.
 * Date: 25.08.2023
 * Time: 00.01
 * Company: Rivera Consulting
 */

namespace App\Filament\Resources\YnabResource\Pages;

use App\Filament\Resources\YnabResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListYnabs extends ListRecords
{
    protected static string $resource = YnabResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
