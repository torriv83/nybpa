<?php

namespace App\Filament\Assistent\Widgets;

use App\Models\Timesheet;
use App\Services\UserStatsService;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class UpcomingWorkTabell extends BaseWidget
{
    protected static ?string $heading = 'Kommende arbeidstider';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()->inFuture('fra_dato')->where('user_id', Auth::user()->id)->where('unavailable', '=', 0)
            )
            ->columns([

                TextColumn::make('fra_dato')->dateTime('d.m.Y, H:i'),
                TextColumn::make('til_dato')->dateTime('d.m.Y, H:i'),
                TextColumn::make('totalt')
                    ->formatStateUsing(fn (int $state): string => (new UserStatsService)->minutesToTime($state))
                    ->summarize(Sum::make()->formatStateUsing(fn (int $state
                    ): string => (new UserStatsService)->minutesToTime($state))),

            ])
            ->emptyStateHeading('Ingen kommende arbeidstider.');
    }
}
