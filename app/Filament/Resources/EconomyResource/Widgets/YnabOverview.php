<?php

namespace App\Filament\Resources\EconomyResource\Widgets;

use Closure;
use permission;
use Filament\Tables;
use App\Models\ynab;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\View\View;

class YnabOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Tall fra YNAB';

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [17];
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'month';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableQuery(): Builder
    {
        return ynab::query();
    }

    protected function getTableContentFooter(): ?View
    {
        $q = ynab::query()->get();

        $income = $q->sum('income');
        $activity = $q->sum('activity');
        $budgeted = $q->sum('budgeted');

        return view('economy.table.table-footer', ['income' => $income, 'activity' => $activity, 'budgeted' => $budgeted]);
    }

    protected function getTableColumns(): array
    {

        return [
            Tables\Columns\TextColumn::make('month')
                ->label('Måned')
                ->date('F, Y')
                ->sortable()
                ->alignLeft(),

            Tables\Columns\TextColumn::make('income')
                ->getStateUsing(function (Model $record) {
                    return $record->income / 1000;
                })
                ->color('success')
                ->money('nok', true)
                ->label('Inntekter')
                ->sortable()
                ->alignLeft(),

            Tables\Columns\TextColumn::make('activity')
                ->getStateUsing(function (Model $record) {
                    return $record->activity / 1000;
                })
                ->color('danger')
                ->money('nok', true)
                ->label('Utgifter')
                ->sortable()
                ->alignLeft(),

            Tables\Columns\TextColumn::make('budgeted')
                ->getStateUsing(function (Model $record) {
                    return $record->budgeted / 1000;
                })
                ->money('nok', true)
                ->label('Budgetert')
                ->sortable()
                ->alignLeft(),

            Tables\Columns\TextColumn::make('Balanse')
                ->getStateUsing(function (Model $record) {
                    return ($record->income + $record->activity)  / 1000;
                })
                ->money('nok', true)
                ->sortable()
                ->alignLeft(),
        ];
    }
}
