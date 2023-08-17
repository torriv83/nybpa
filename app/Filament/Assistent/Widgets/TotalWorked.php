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

        $timeWorkedThisYearFormatted = $timeWorkedThisYear
            ? (new \App\Services\UserStatsService)->minutesToTime($timeWorkedThisYear)
            : 'Ingen timer registrert'; // Default text or value

        $timeWorkedThisMonthFormatted = $timeWorkedThisMonth
            ? (new \App\Services\UserStatsService)->minutesToTime($timeWorkedThisMonth)
            : 'Ingen timer registrert'; // Default text or value

        $nextWorkTimeFormatted = $nextWorkTime
            ? Carbon::make($nextWorkTime->fra_dato)->format('H:i') . ' - ' . Carbon::make($nextWorkTime->til_dato)->format('H:i')
            : 'Ingen kommende arbeidstider'; // Default text or value

        $nextWorkTimeDescription = $nextWorkTime
            ? Carbon::make($nextWorkTime->fra_dato)->format('d.m.Y')
            : '';

        return [
            Stat::make('Neste arbeidstid', $nextWorkTimeFormatted)
                ->description($nextWorkTimeDescription),
            Stat::make('Timer jobbet denne mnd', $timeWorkedThisMonthFormatted),
            Stat::make('Timer jobbet i år', $timeWorkedThisYearFormatted),
        ];

    }
}
