<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Closure;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Ansatte extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Ansatte';

    protected static ?int $sort = 7;

    protected array|string|int $columnSpan = 3;

    public function getTableRecordKey(Model $record): string
    {
        return $record;
    }

    protected function getTableQuery(): Builder
    {
        return User::query()->with('timesheet')->assistenter();
    }


    protected function getTableRecordUrlUsing(): Closure
    {
        return fn(Model $record): string => route('filament.admin.resources.users.view', ['record' => $record]);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Navn'),
            Tables\Columns\TextColumn::make('roles.name')
                ->badge()
                ->label('Stilling')
                ->colors([
                    'success',
                    'primary' => 'Tilkalling',
                ]),
            Tables\Columns\TextColumn::make('email')
                ->label('E-post')
                ->limit(10)
                ->tooltip(fn(Model $record): string => "$record->email"),
            Tables\Columns\TextColumn::make('phone')
                ->label('Telefon')
                ->default('12345678'),
            Tables\Columns\TextColumn::make('Jobbet i år')
                ->getStateUsing(function (Model $record) {
                    $minutes = Cache::remember('WorkedThisYear' . $record->id, now()->addDay(), function () use ($record) {
                        return $record->timesheet()
                            ->yearToDate('fra_dato')
                            ->where('unavailable', '!=', 1)->sum('totalt');
                    });

                    return sprintf('%02d', intdiv($minutes, 60)) . ':' . (sprintf('%02d', $minutes % 60));
                })
                ->label('Jobbet i år')
                ->default('0'),
        ];
    }
}
