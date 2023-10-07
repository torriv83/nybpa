<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Timesheet;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

use function strip_tags;

class AnsattKanIkkeJobbe extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Ansatte kan ikke jobbe';

    protected static ?int $sort = 6;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()->where('unavailable', '=', 1)->inFuture('til_dato')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Hvem')
                    ->tooltip(fn(Model $record): string => strip_tags("$record->description")),
                /* TODO trenger jeg denne? */
                /* Tables\Columns\TextColumn::make('fra_dato')
                    ->date('d.m.Y')
                    ->label('Dato'),*/
                Tables\Columns\TextColumn::make('fra_dato')
                    ->getStateUsing(function (Model $record) {
                        if ($record->allDay == 1) {
                            return Carbon::parse($record->fra_dato)->format('d.m.Y');
                        } else {
                            return Carbon::parse($record->fra_dato)->format('d.m.Y, H:i');
                        }
                    })
                    ->sortable()
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
                    ->sortable()
                    ->tooltip(fn(Model $record): string => strip_tags("$record->description"))
                    ->label('Til'),
                IconColumn::make('allDay')
                    ->label('Hele dagen?')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
            ])
            ->defaultSort('fra_dato')
            ->emptyStateHeading('Alle kan jobbe!')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateDescription('Opprett en tid under')
            ->emptyStateActions([
                Action::make('create')
                    ->label('Legg til')
                    ->url(route('filament.admin.resources.timelister.create'))
                    ->icon('heroicon-m-plus')
                    ->button(),
            ]);
    }
}
