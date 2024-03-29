<?php

namespace App\Filament\Privat\Resources\EconomyResource\Pages;

use App\Filament\Privat\Resources\EconomyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;

class ManageEconomies extends ManageRecords
{
    protected static string $resource = EconomyResource::class;

/*    public function getHeaderWidgetsColumns(): int|array
    {
        return 12;
    }*/

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EconomyResource\Widgets\StatsOverview::class,
            EconomyResource\Widgets\AccountsOverview::class,
            EconomyResource\Widgets\YnabChart::class,
            EconomyResource\Widgets\YnabOverview::class,
        ];
    }

    public function updated($name): void
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->dispatch('updateStatsOverview');
        }
    }
}
