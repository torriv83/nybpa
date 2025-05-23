<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Timesheet;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class TimerIUka extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Timer i uka';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()
                    ->selectRaw('id, FROM_DAYS(TO_DAYS(timesheets.fra_dato) - MOD(TO_DAYS(timesheets.fra_dato) - 2, 7)) AS Uke')
                    ->selectRaw('SUM(timesheets.totalt) AS Totalt, AVG(timesheets.totalt) AS Gjennomsnitt, COUNT(*) AS Antall')
                    ->groupByRaw('WEEK(timesheets.fra_dato, 7)')
                    ->yearToDate('timesheets.fra_dato')
                    ->where('timesheets.unavailable', '!=', 1)
            )
            ->columns([
                TextColumn::make('Uke')
                    ->formatStateUsing(fn (string $state): string => __(Carbon::parse($state)->week()))
                    ->label('Uke')
                    ->sortable(),
                TextColumn::make('Totalt')
                    ->formatStateUsing(fn (string $state): string => __(date('H:i', mktime(0, $state))))
                    ->label('Totalt')
                    ->sortable(),
                TextColumn::make('Gjennomsnitt')
                    ->formatStateUsing(fn (string $state): string => __(date('H:i', mktime(0, (int) floatval($state)))))
                    ->sortable(),
                TextColumn::make('Antall')
                    ->sortable(),
            ])->paginated([3, 4, 8, 12, 24, 36])->defaultSort('Uke', 'desc');
    }
}
