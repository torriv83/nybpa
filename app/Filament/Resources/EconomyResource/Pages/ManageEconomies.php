<?php

namespace App\Filament\Resources\EconomyResource\Pages;

use App\Filament\Resources\EconomyResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageEconomies extends ManageRecords
{

    protected static string $resource = EconomyResource::class;

    public function getHeaderWidgetsColumns(): int|array
    {
        return 12;
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EconomyResource\Widgets\StatsOverview::class,
            EconomyResource\Widgets\AccountsOverview::class,
            EconomyResource\Widgets\YnabOverview::class,
        ];
    }

    public function updated($name)
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->emit('updateStatsOverview');
        }
    }
}
