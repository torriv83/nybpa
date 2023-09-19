<?php

namespace App\Filament\Admin\Resources\TimesheetResource\Widgets;

use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HoursUsedEachMonth extends BaseWidget
{

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';
    protected static ?string   $heading    = '';


    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()
                    ->select(DB::raw('id, DATE(DATE_FORMAT(fra_dato, \'%Y-%m-01\')) AS month, SUM(totalt) AS Totalt'))
                    ->groupBy(DB::raw("DATE(DATE_FORMAT(fra_dato, '%Y-%m-01'))"))
                    ->whereBetween(
                        'fra_dato',
                        [
                            Carbon::parse('first day of January')->format('Y-m-d H:i:s'),
                            Carbon::now()->endOfYear(),
                        ]
                    )
                    ->where('unavailable', '!=', 1)
            )
            ->columns([
                Split::make([
                    Stack::make([
                        Tables\Columns\TextColumn::make('month')
                            ->formatStateUsing(function ($state) {
                                return ucfirst(Carbon::parse($state)->locale('no')->monthName);
                            }),
                        Tables\Columns\TextColumn::make('Totalt')
                            ->formatStateUsing(function ($state) {
                                return Cache::tags(['timesheet'])->remember('Totalt-' . $state, now()->addMonth(), function () use ($state) {
                                    return $state / 60;
                                });
                            }),
                    ])
                ])
            ])
            ->paginated([1, 3, 6, 9, 12])
            ->contentGrid(['md' => 12]);
    }

}
