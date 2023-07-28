<?php

namespace App\Filament\Widgets;

use App\Services\UserStatsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class UserStats extends BaseWidget
{
    private UserStatsService $userStatsService;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'col-span-3 sm:col-span-3 md:col-span-3 lg:col-span-6';

    //        [
    //        'xs' => 6,
    //        'sm' => 3,
    //        'md' => 6,
    //        'xl' => 6,
    //    ];

    //        'xs:grid-cols-12 sm:grid-cols-12 md:grid-cols-12 lg:grid-cols-12';

    public function __construct()
    {
        parent::__construct();
        $this->userStatsService = new UserStatsService();
    }

    protected function getColumns(): int
    {
        return 4;
    }

    protected function getCards(): array
    {

        return [
            Card::make('Antall Assistenter', $this->userStatsService->getNumberOfAssistents())
                ->url(route('filament.resources.users.index')),

            Card::make('Timer brukt i år', $this->userStatsService->getHoursUsedThisYear())
                ->chart($this->userStatsService->getYearlyTimeChart())
                ->color('success')
                ->url(route('filament.resources.timesheets.index', $this->userStatsService->getYearlyTimeFilters()))
                ->description($this->userStatsService->getHoursUsedThisMonthDescription()),

            Card::make('Timer igjen', $this->userStatsService->getRemainingHours())
                ->description($this->userStatsService->getAverageHoursPerWeekDescription())
                ->color('success'),

            Card::make('Antall utstyr på lista', $this->userStatsService->getNumberOfEquipment()),
        ];
    }
}
