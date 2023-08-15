<?php
/**
 * Created by ${USER}.
 * Date: 18.04.2023
 * Time: 06.45
 * Company: Rivera Consulting
 */

namespace App\Filament\Resources\WeekplanResource\Pages;

use App\Filament\Resources\WeekplanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWeekplans extends ListRecords
{
    protected static string $resource = WeekplanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
