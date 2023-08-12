<?php

namespace App\Filament\Widgets;

use App\Models\Timesheet;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use function strip_tags;

class AnsattKanIkkeJobbe extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Ansatte kan ikke jobbe';

    protected static ?int $sort = 6;

    protected array|string|int $columnSpan = 3;

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Alle kan jobbe';
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record;
    }

    protected function getTableQuery(): Builder
    {
        return Timesheet::query()->where('unavailable', '=', 1)->inFuture('til_dato');
    }

    protected function getTableColumns(): array
    {

        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label('Hvem')
                ->tooltip(fn(Model $record): string => strip_tags("$record->description")),
            Tables\Columns\TextColumn::make('fra_dato')
                ->date('d.m.Y')
                ->label('Dato'),
            Tables\Columns\TextColumn::make('fra_dato')
                ->getStateUsing(function (Model $record) {
                    if ($record->allDay == 1) {
                        return Carbon::parse($record->fra_dato)->format('d.m.Y');
                    } else {
                        return Carbon::parse($record->fra_dato)->format('d.m.Y, H:i');
                    }
                })
                ->label('Fra')
                ->tooltip(fn(Model $record): string => strip_tags("$record->description")),
            Tables\Columns\TextColumn::make('til_dato')
                ->getStateUsing(function (Model $record) {
                    if ($record->allDay == 1) {
                        return Carbon::parse($record->til_dato)->format('d.m.Y');
                    } else {
                        return Carbon::parse($record->til_dato)->format('d.m.Y, H:i');
                    }
                })
                ->tooltip(fn(Model $record): string => strip_tags("$record->description"))
                ->label('Til'),
            IconColumn::make('allDay')
                ->label('Hele dagen?')
                ->options([
                    'heroicon-o-x-circle',
                    'heroicon-o-check-circle' => fn($state): bool => $state === 1,
                ])
                ->colors([
                    'danger',
                    'success' => 1,
                ]),
        ];
    }
}
