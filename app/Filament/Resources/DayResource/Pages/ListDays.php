<?php
/**
 * Created by ${USER}.
 * Date: 18.04.2023
 * Time: 06.48
 * Company: Rivera Consulting
 */

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDays extends ListRecords
{
    protected static string $resource = DayResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
