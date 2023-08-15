<?php
/**
 * Created by ${USER}.
 * Date: 18.04.2023
 * Time: 06.45
 * Company: Rivera Consulting
 */

namespace App\Filament\Resources\WeekplanResource\Pages;

use App\Filament\Resources\WeekplanResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditWeekplan extends EditRecord
{
    protected static string $resource = WeekplanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
