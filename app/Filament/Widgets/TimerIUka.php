<?php

namespace App\Filament\Widgets;

use Closure;
use Filament\Tables;
use App\Models\Timesheet;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class TimerIUka extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static ?string $heading = 'Timer i uka';
    protected static ?int $sort = 3;
    protected array|string|int $columnSpan = 4;

    protected function getTableRecordsPerPageSelectOptions(): array 
    {
        return [4, 8, 12, 36];
    } 

    public function getTableRecordKey(Model $record): string
    {
        return uniqid();
    }
    
    protected function getTableQuery(): Builder
    {

        return Timesheet::query()
                ->select(\DB::raw('FROM_DAYS(TO_DAYS(fra_dato) -MOD(TO_DAYS(fra_dato) -2, 7)) AS Uke, SUM(totalt) AS Totalt, AVG(totalt) AS Gjennomsnitt, COUNT(*) AS Antall'))
                ->groupBy(\DB::raw('WEEK(fra_dato,7)'))
                ->orderBy('Uke', 'asc')
                ->whereBetween('fra_dato', [Carbon::parse('first day of January')
                                ->format('Y-m-d H:i:s'), Carbon::now()->endOfYear()]
                            )
                ->where('unavailable', '!=', 1);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('Uke')
                ->formatStateUsing(fn (string $state): ?string => __(Carbon::parse($state)->week()))
                ->label('Uke'),
            Tables\Columns\TextColumn::make('Totalt')
                ->formatStateUsing(fn (string $state): string => __(date('H:i',mktime(0,$state))))
                ->label('Totalt'),
            Tables\Columns\TextColumn::make('Gjennomsnitt')
                ->formatStateUsing(fn (string $state): string => __(date('H:i',mktime(0,$state)))),
            Tables\Columns\TextColumn::make('Antall'),
        ];
    }
}