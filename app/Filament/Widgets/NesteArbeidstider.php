<?php

namespace App\Filament\Widgets;

use App\Models\Timesheet;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class NesteArbeidstider extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'De neste arbeidstidene';

    protected static ?int $sort = 5;

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Ingen planlagte tider enda';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()->inFuture('fra_dato')->whereDoesntHave('user', function ($query) {
                    $query->role('admin');
                })->where('unavailable', '=', 0)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Hvem'),
                Tables\Columns\TextColumn::make('fra_dato')
                    ->dateTime('d.m.Y, H:i')
                    ->label('Fra'),
                Tables\Columns\TextColumn::make('til_dato')
                    ->dateTime('d.m.Y, H:i')
                    ->label('Til'),
            ])->defaultSort('fra_dato', 'asc');
    }
}
