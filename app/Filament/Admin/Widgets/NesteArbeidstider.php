<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Timesheet;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class NesteArbeidstider extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'De neste arbeidstidene';

    protected static ?int $sort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()
                    ->inFuture('fra_dato')
                    ->whereDoesntHave('user', fn ($query) => $query->whereHas('roles', fn ($query) => $query->where('name', 'admin')
                    )
                    )
                    ->where('unavailable', '=', 0)
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
            ])->defaultSort('fra_dato', 'asc')
            ->emptyStateHeading('Ingen kommende arbeid')
            ->emptyStateDescription('Legg til arbeid under.')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Legg til')
                    ->url(route('filament.admin.resources.timelister.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ]);
    }
}
