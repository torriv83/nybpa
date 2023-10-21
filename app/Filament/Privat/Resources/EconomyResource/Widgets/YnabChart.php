<?php

namespace App\Filament\Privat\Resources\EconomyResource\Widgets;

use App\Models\Ynab;
use Filament\Widgets\ChartWidget;

class YnabChart extends ChartWidget
{
    protected static ?string $heading = 'YNAB Chart';
    protected static ?string $pollingInterval = null;
    protected int|string|array $columnSpan = 12;
    protected static ?string $maxHeight = '500px';

    protected static ?array $options = [
        'interaction' => [
            'intersect' => false,
            'mode' => 'index',
        ],
        'scales' => [
            'y' => [
                'beginAtZero' => false
            ]
        ],
    ];

    protected function getData(): array
    {
        $startMonth = now()->subMonths(12)->startOfMonth();
        $endMonth = now()->endOfMonth();

        $ynab = Ynab::whereBetween('month', [$startMonth, $endMonth])
            ->orderBy('month', 'asc')
            ->get()
            ->groupBy(function ($date) {
                return date('M Y', strtotime($date->month));  // Group by abbreviated month name and year
            });

        // Initialize arrays for labels and data
        $labels = $ynab->keys()->toArray();
        $incomeData = [];
        $activityData = [];
        $budgetedData = [];  // Initialize array for budgeted data

        foreach ($ynab as $month => $items)
        {
            $incomeData[] = $items->sum('income') / 1000;
            $activityData[] = abs($items->sum('activity')) / 1000;
            $budgetedData[] = $items->sum('budgeted') / 1000;  // Sum and append budgeted data
        }

        $incomeMinusActivityData = array_map(
            function ($income, $activity) {
                return $income - $activity;
            },
            $incomeData,
            $activityData
        );

        return [
            'datasets' => [
                [
                    'label' => 'Utgift',
                    'data' => $activityData,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)', // Bootstrap's red color
                    'borderColor' => 'rgb(255, 99, 132)',     // Lighter red for border
                ],
                [
                    'label' => 'Inntekt',
                    'data' => $incomeData,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)', // Bootstrap's green color
                    'borderColor' => 'rgb(54, 162, 235)',     // Lighter green for border
                ],
                [
                    'label' => 'Budgetert',  // Label for budgeted data
                    'data' => $budgetedData,
                    'backgroundColor' => 'rgba(201, 203, 207, 0.2)', // Bootstrap's gray color
                    'borderColor' => 'rgb(201, 203, 207)',    // Lighter gray for border
                ],
                [
                    'type' => 'line',
                    'label' => 'Inntekt - Utgift',
                    'data' => $incomeMinusActivityData,
                    'backgroundColor' => 'rgba(201, 203, 207, 0.2)',
                    'borderColor' => 'rgb(201, 203, 207)',
                    'fill' => false,  // This option ensures the area under the line is not filled
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
