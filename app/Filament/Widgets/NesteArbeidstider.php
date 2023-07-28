<?php

namespace App\Filament\Widgets;

use App\Models\Timesheet;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class NesteArbeidstider extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'De neste arbeidstidene';

    protected static ?int $sort = 5;

    protected array|string|int $columnSpan = 3;

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Ingen planlagte tider enda';
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record;
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'asc';
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'fra_dato';
    }

    protected function getTableQuery(): Builder
    {
        return Timesheet::query()->inFuture('fra_dato')->where('user_id', '!=', 1)->where('unavailable', '=', 0);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label('Hvem'),
            Tables\Columns\TextColumn::make('fra_dato')
                ->dateTime('d.m.Y, H:i')
                ->label('Fra'),
            Tables\Columns\TextColumn::make('til_dato')
                ->dateTime('d.m.Y, H:i')
                ->label('Til'),
        ];
    }
}
