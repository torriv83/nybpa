<?php

namespace App\Filament\Resources\TimesheetResource\Widgets;

use Carbon\Carbon;
use Filament\Tables;
use App\Models\Timesheet;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class HoursUsedEachMonth extends Widget implements Tables\Contracts\HasTable
{

    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.resources.timesheet-resource.widgets.hours-used-each-month';
    protected static ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';

    public function getTableRecordKey(Model $record): string
    {
        return $record;
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableContentGrid(): ?array
    {
        return [
            'md' => 12,
        ];
    }

    protected function getTableQuery(): Builder
    {

        return Timesheet::query()->select(DB::raw('DATE(DATE_FORMAT(fra_dato, \'%Y-%m-01\')) AS month, SUM(totalt) AS Totalt'))
            ->groupBy(DB::raw("DATE(DATE_FORMAT(fra_dato, '%Y-%m-01'))"))
            ->whereBetween(
                'fra_dato',
                [Carbon::parse('first day of January')
                    ->format('Y-m-d H:i:s'), Carbon::now()->endOfYear()]
            )
            ->where('unavailable', '!=', 1);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('month')
                ->formatStateUsing(function ($state) {
                    return ucfirst(Carbon::parse($state)->locale('no')->monthName);
                }),
            Tables\Columns\TextColumn::make('Totalt')
                ->formatStateUsing(function ($state) {
                    return $state / 60;
                }),
        ];
    }

}
