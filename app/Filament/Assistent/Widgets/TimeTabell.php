<?php

namespace App\Filament\Assistent\Widgets;

use App\Models\Timesheet;
use Carbon\Carbon;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TimeTabell extends BaseWidget
{

    protected static ?string $heading         = 'Time tabell';
    protected static ?string $pollingInterval = null;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()->where('user_id', Auth::user()->id)->where('unavailable', '!=', 1)
            )
            ->columns([
                TextColumn::make('fra_dato')->dateTime('d.m.Y, H:i')->sortable(),
                TextColumn::make('til_dato')->dateTime('d.m.Y, H:i')->sortable(),
                TextColumn::make('totalt')->sortable()
                    ->formatStateUsing(fn(string $state): string => (new \App\Services\UserStatsService)->minutesToTime($state))
                    ->summarize(Sum::make()->formatStateUsing(fn(string $state
                    ): string => (new \App\Services\UserStatsService)->minutesToTime($state))),

            ])->filters([
                Filter::make('Forrige måned')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('fra_dato', '<=', Carbon::now()->subMonth()->endOfMonth())
                        ->where('til_dato', '>=', Carbon::now()->subMonth()->startOfMonth())),

                Filter::make('Denne måneden')
                    ->query(fn(Builder $query): Builder => $query
                        ->where('fra_dato', '<=', Carbon::now()->endOfMonth())
                        ->where('til_dato', '>=', Carbon::now()->startOfMonth()))->default(),
            ]);
    }
}
