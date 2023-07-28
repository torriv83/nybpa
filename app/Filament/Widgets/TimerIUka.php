<?php

namespace App\Filament\Widgets;

use App\Models\Timesheet;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TimerIUka extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Timer i uka';

    protected static ?int $sort = 3;

    protected array|string|int $columnSpan = 3;

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [3, 4, 8, 12, 24, 36];
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'Uke';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableQuery(): Builder
    {

        return Timesheet::query()
            ->selectRaw('FROM_DAYS(TO_DAYS(timesheets.fra_dato) - MOD(TO_DAYS(timesheets.fra_dato) - 2, 7)) AS Uke')
            ->selectRaw('SUM(timesheets.totalt) AS Totalt, AVG(timesheets.totalt) AS Gjennomsnitt, COUNT(*) AS Antall')
            ->groupByRaw('WEEK(timesheets.fra_dato, 7)')
            ->yearToDate('timesheets.fra_dato')
            ->where('timesheets.unavailable', '!=', 1);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('Uke')
                ->formatStateUsing(fn (string $state): ?string => __(Carbon::parse($state)->week()))
                ->label('Uke')
                ->sortable(),
            TextColumn::make('Totalt')
                ->formatStateUsing(fn (string $state): string => __(date('H:i', mktime(0, $state))))
                ->label('Totalt')
                ->sortable(),
            TextColumn::make('Gjennomsnitt')
                ->formatStateUsing(fn (string $state): string => __(date('H:i', mktime(0, $state))))
                ->sortable(),
            TextColumn::make('Antall')
                ->sortable(),
        ];
    }
}
