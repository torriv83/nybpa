<?php

namespace App\Filament\Resources\EconomyResource\Widgets;

use App\Models\Ynab;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

//use permission;

class YnabOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Tall fra YNAB';

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ynab::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Måned')
                    ->formatStateUsing(function ($column): ?string {

                        Carbon::setlocale(config('app.locale'));

                        return ucfirst(Carbon::parse($column->getState())->translatedFormat('F, Y'));
                    })
                    ->sortable()
                    ->alignLeft(),

                Tables\Columns\TextColumn::make('income')
                    ->getStateUsing(function (Model $record) {
                        return $record->income / 1000;
                    })
                    ->money('nok', true)
                    ->label('Inntekter')
                    ->sortable()
                    ->alignLeft(),
                //->summarize(Average::make()),

                Tables\Columns\TextColumn::make('activity')
                    ->getStateUsing(function (Model $record) {
                        return $record->activity / 1000;
                    })
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

                Tables\Columns\TextColumn::make('inntektutgift')
                    ->getStateUsing(function (Model $record) {
                        return ($record->income + $record->activity) / 1000;
                    })
                    ->money('nok', true)
                    ->sortable()
                    ->color(function ($record) {
                        if (($record->income + $record->activity) / 1000 < 0) {
                            return 'danger';
                        } else {
                            return 'success';
                        }
                    })
                    ->alignLeft()->label('Inntekt - Utgift'),

                Tables\Columns\TextColumn::make('Balanse')
                    ->getStateUsing(function (Model $record) {
                        return ($record->income - $record->budgeted) / 1000;
                    })
                    ->money('nok', true)
                    ->sortable()
                    ->color(function ($record) {
                        if (($record->income - $record->budgeted) / 1000 < 0) {
                            return 'danger';
                        } else {
                            return 'success';
                        }
                    })
                    ->alignLeft(),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('month', 'desc')
            ->paginated([12, 25, 50, 100, 'all'])
            ->filters([
                Tables\Filters\Filter::make('month')
                    ->form([
                        Forms\Components\DatePicker::make('fra'),
                        Forms\Components\DatePicker::make('til')->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['fra'],
                                fn(Builder $query, $date): Builder => $query->whereDate('month', '>=', $date),
                            )
                            ->when(
                                $data['til'],
                                fn(Builder $query, $date): Builder => $query->whereDate('month', '<=', $date),
                            );
                    }),
                Tables\Filters\Filter::make('last3months')->label('Siste 3 måneder')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('month', '>=', Carbon::now()->subMonths(3))),
                Tables\Filters\Filter::make('last6months')->label('Siste 6 måneder')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('month', '>=', Carbon::now()->subMonths(6))),
                Tables\Filters\Filter::make('lastyear')->label('Siste år')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('month', '>=', Carbon::now()->subMonths(12)))->default(),
            ]);
    }

    //TODO endre denne
    protected function getTableContentFooter(): ?View
    {
        $query = Ynab::query()->get();

        $income      = $query->sum('income');
        $activity    = $query->sum('activity');
        $budgeted    = $query->sum('budgeted');
        $avgincome   = $query->avg('income');
        $avgactivity = $query->avg('activity');
        $avgbudgeted = $query->avg('budgeted');

        return view('economy.table.table-footer', [
            'income'      => $income,
            'activity'    => $activity,
            'budgeted'    => $budgeted,
            'avgincome'   => $avgincome,
            'avgbudgeted' => $avgbudgeted,
            'avgactivity' => $avgactivity,
        ]);
    }
}
