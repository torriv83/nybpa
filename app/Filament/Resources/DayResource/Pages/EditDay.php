<?php
/**
 * Created by ${USER}.
 * Date: 18.04.2023
 * Time: 06.48
 * Company: Rivera Consulting
 */

namespace App\Filament\Resources\DayResource\Pages;

use App\Filament\Resources\DayResource;
use Filament\Pages\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDay extends EditRecord
{
    protected static string $resource = DayResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
