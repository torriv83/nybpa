<?php

namespace App\Filament\Widgets;

use App\Filament\Privat\Resources\UtstyrResource;
use App\Filament\Resources\TimesheetResource;
use App\Filament\Resources\UserResource;
use App\Services\UserStatsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStats extends BaseWidget
{
    private UserStatsService $userStatsService;

    protected static ?string $pollingInterval = null;
    protected static ?int    $sort            = 1;

    public function __construct()
    {
        $this->userStatsService = new UserStatsService();
    }

    protected function getStats(): array
    {

        return [
            Stat::make('Antall Assistenter', $this->userStatsService->getNumberOfAssistents())
                ->url(UserResource::getUrl()),

            Stat::make('Timer brukt i år', $this->userStatsService->getHoursUsedThisYear())
                ->chart($this->userStatsService->getYearlyTimeChart())
                ->color('success')
                ->url(TimesheetResource::getUrl('index',
                    $this->userStatsService->getYearlyTimeFilters()))
                ->description($this->userStatsService->getHoursUsedThisMonthDescription()),

            Stat::make('Timer igjen', $this->userStatsService->getRemainingHours())
                ->description($this->userStatsService->getAverageHoursPerWeekDescription())
                ->color('success'),

            Stat::make('Antall utstyr på lista', UtstyrResource::getEloquentQuery()->where('deleted_at', null)->count())

        ];
    }
}
