<?php

namespace App\Filament\Widgets;

use Closure;
use permission;
use App\Models\User;
use Filament\Tables;
use App\Models\Timesheet;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class AnsattKanIkkeJobbe extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static ?string $heading         = 'Ansatte kan ikke jobbe';
    protected static ?int $sort               = 6;
    protected array|string|int $columnSpan    = 6;

    public function getTableRecordKey(Model $record): string
    {
        return uniqid();
    }
    
    protected function getTableQuery(): Builder
    {
        return Timesheet::query()->where('unavailable', '=', 1);   
    }

    protected function getTableColumns(): array
    {

        return [
            Tables\Columns\TextColumn::make('user.name')
                ->label('Hvem')
                ->tooltip(fn (Model $record): string => \strip_tags("{$record->description}")),
            Tables\Columns\TextColumn::make('fra_dato')
                ->date('d.m.Y')    
                ->label('Dato'),
            Tables\Columns\TextColumn::make('fra_dato')
                ->getStateUsing(function(Model $record) {
                    if($record->allDay == 1){
                        return Carbon::parse($record->fra_dato)->format('d.m.Y');
                    }else{
                        return Carbon::parse($record->fra_dato)->format('d.m.Y, H:i');
                    }
                })
                ->label('Fra')
                ->tooltip(fn (Model $record): string => \strip_tags("{$record->description}")),
            Tables\Columns\TextColumn::make('til_dato')
                ->getStateUsing(function(Model $record) {
                    if($record->allDay == 1){
                        return Carbon::parse($record->til_dato)->format('d.m.Y');
                    }else{
                        return Carbon::parse($record->til_dato)->format('d.m.Y, H:i');
                    }
                })
                ->tooltip(fn (Model $record): string => \strip_tags("{$record->description}"))
                ->label('Til'),
            IconColumn::make('allDay')
                ->label('Hele dagen?')
                ->options([
                    'heroicon-o-x-circle',
                    'heroicon-o-check-circle' => fn ($state): bool => $state === 1,
                ])
                ->colors([
                    'danger',
                    'success' => 1,
                ])
        ];
    }
}