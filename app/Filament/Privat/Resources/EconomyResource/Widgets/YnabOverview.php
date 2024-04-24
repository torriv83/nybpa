<?php

namespace App\Filament\Privat\Resources\EconomyResource\Widgets;

use App\Models\Ynab;
use App\Traits\Economy;
use Carbon\Carbon;
use Exception;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as sumBuilder;

//use permission;

class YnabOverview extends BaseWidget
{
    use Economy;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = '12';

    /**
     * @throws Exception
     */
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ynab::query()
            )
            ->heading('Tall fra YNAB')
            ->description(fn() => 'Sist oppdatert: '.Ynab::latest('updated_at')->first()->updated_at->format('d.m.Y H:i:s'))
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->label('Måned')
                    ->formatStateUsing(function ($column): ?string
                    {
                        Carbon::setlocale(config('app.locale'));

                        return ucfirst(Carbon::parse($column->getState())->translatedFormat('F, Y'));
                    })
                    ->sortable()
                    ->alignLeft(),

                Tables\Columns\TextColumn::make('income')
                    ->money('nok')
                    ->label('Inntekter')
                    ->sortable()
                    ->alignLeft()
                    ->summarize(Sum::make()->money('NOK', divideBy: 1000))
/*                    ->summarize(Summarizer::make()
                        ->label('Sum')
                        ->using(fn(sumBuilder $query): float => $this->divideYnab($query->sum('income')))
                        ->money('nok')
                    )*/
                    ->summarize(Average::make()->money('NOK', divideBy: 1000))
                    /*->summarize(Summarizer::make()
                        ->label('Gjennomsnitt')
                        ->using(fn(sumBuilder $query): float => $this->divideYnab($query->avg('income')))
                        ->money('nok')
                    )*/,

                Tables\Columns\TextColumn::make('activity')
                    ->money('nok', true)
                    ->label('Utgifter')
                    ->sortable()
                    ->alignLeft()
                    ->summarize(Sum::make()->money('NOK', divideBy: 1000))
                    ->summarize(Average::make()->money('NOK', divideBy: 1000))
                    /*->summarize(Summarizer::make()
                        ->label('Sum')
                        ->using(fn(sumBuilder $query): float => $this->divideYnab($query->sum('activity')))
                        ->money('nok')
                    )*/
                    /*->summarize(Summarizer::make()
                        ->label('Gjennomsnitt')
                        ->using(fn(sumBuilder $query): float => $this->divideYnab($query->avg('activity')))
                        ->money('nok')
                    )*/,

                Tables\Columns\TextColumn::make('budgeted')
                    ->money('nok', true)
                    ->label('Budgetert')
                    ->sortable()
                    ->alignLeft()
                    ->summarize(Sum::make()->money('NOK', divideBy: 1000))
                    ->summarize(Average::make()->money('NOK', divideBy: 1000))
                    /*->summarize(Summarizer::make()
                        ->label('Sum')
                        ->using(fn(sumBuilder $query): float => $this->divideYnab($query->sum('budgeted')))
                        ->money('nok')
                    )*/
                    /*->summarize(Summarizer::make()
                        ->label('Gjennomsnitt')
                        ->using(fn(sumBuilder $query): float => $this->divideYnab($query->avg('budgeted')))
                        ->money('nok')
                    )*/,

                Tables\Columns\TextColumn::make('inntektutgift')
                    ->getStateUsing(function (Model $record){
                        return ($record->income + $record->activity);
                    })
                    ->money('nok', true)
                    ->sortable()
                    ->color(function ($record){
                        if (($record->income + $record->activity) < 0)
                        {
                            return 'danger';
                        } else
                        {
                            return 'success';
                        }
                    })
                    ->alignLeft()
                    ->label('Inntekt - Utgift')
                    ->summarize(Summarizer::make()
                        ->label('Sum')
                        ->using(fn(sumBuilder $query): float =>
                            $this->divideYnab($query->sum('income')) + $this->divideYnab($query->sum('activity')))
                        ->money('nok')
                    )
                    ->summarize(Summarizer::make()
                        ->label('Gjennomsnitt')
                        ->using(fn(sumBuilder $query): float =>
                            $this->divideYnab($query->avg('income')) + $this->divideYnab($query->avg('activity')))
                        ->money('nok')
                    ),

                Tables\Columns\TextColumn::make('Balanse')
                    ->getStateUsing(function (Model $record){
                        return ($record->income - $record->budgeted);
                    })
                    ->money('nok', true)
                    ->sortable()
                    ->color(function ($record){
                        if (($record->income - $record->budgeted) < 0)
                        {
                            return 'danger';
                        } else
                        {
                            return 'success';
                        }
                    })
                    ->alignLeft()
                    ->summarize(Summarizer::make()
                        ->label('Sum')
                        ->using(fn(sumBuilder $query): float =>
                            $this->divideYnab($query->sum('income')) - $this->divideYnab($query->sum('budgeted')))
                        ->money('nok')
                    )
                    ->summarize(Summarizer::make()
                        ->label('Gjennomsnitt')
                        ->using(fn(sumBuilder $query): float =>
                            $this->divideYnab($query->avg('income')) - $this->divideYnab($query->avg('budgeted')))
                        ->money('nok')
                    ),
            ])
            ->striped()
            ->deferLoading()
            ->defaultSort('month', 'desc')
            ->paginated([12, 25, 50, 100, 'all'])
            ->headerActions([
                Action::make('Oppdater Ynab')->action('updateYnab'),
            ])
            ->filters([
                Tables\Filters\Filter::make('month')
                    ->form([
                        Forms\Components\DatePicker::make('fra'),
                        Forms\Components\DatePicker::make('til')->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder
                    {
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

    public function updateYnab(): void
    {
        Ynab::fetchData();
        Notification::make()
            ->title('Ynab er oppdatert')
            ->success()
            ->send();
    }

}
