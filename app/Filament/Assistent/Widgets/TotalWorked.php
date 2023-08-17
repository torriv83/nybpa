<?php

namespace App\Filament\Assistent\Widgets;

use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TotalWorked extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {

        $timeSheet           = new Timesheet();
        $timeWorkedThisYear  = $timeSheet->where('user_id', Auth::user()->id)->where('unavailable', 0)->thisYear()->sum('totalt');
        $timeWorkedThisMonth = $timeSheet->where('user_id', Auth::user()->id)->where('unavailable', 0)->thisYear()->thisMonth()->sum('totalt');
        $nextWorkTime        = $timeSheet->where('user_id', Auth::user()->id)->where('unavailable', 0)->where('fra_dato', '>=', now())->first();

        return [
            Stat::make('Neste arbeidstid',
                Carbon::make($nextWorkTime->fra_dato)->format('H:i') . ' - ' . Carbon::make($nextWorkTime->til_dato)->format('H:i'))
                ->description(Carbon::make($nextWorkTime->fra_dato)->format('d.m.Y')),
            Stat::make('Timer jobbet denne mnd', (new \App\Services\UserStatsService)->minutesToTime($timeWorkedThisMonth)),
            Stat::make('Timer jobbet i år', (new \App\Services\UserStatsService)->minutesToTime($timeWorkedThisYear)),
        ];
    }
}
