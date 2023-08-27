<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Ansatte extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected static ?string $heading = 'Ansatte';

    protected static ?int $sort = 7;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()->with('timesheet')->assistenter()
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Navn')->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->label('Stilling')
                    ->colors([
                        'success',
                        'primary' => 'Tilkalling',
                    ])->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('E-post')
                    ->limit(10)
                    ->tooltip(fn(Model $record): string => "$record->email"),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->default('12345678'),
                Tables\Columns\TextColumn::make('Jobbet i år')
                    ->getStateUsing(function (Model $record) {
                        $minutes = Cache::tags(['timesheet'])->remember('WorkedThisYear' . $record->id, now()->addDay(), function () use ($record) {
                            return $record->timesheet()
                                ->yearToDate('fra_dato')
                                ->where('unavailable', '!=', 1)->sum('totalt');
                        });

                        return sprintf('%02d', intdiv($minutes, 60)) . ':' . (sprintf('%02d', $minutes % 60));
                    })
                    ->label('Jobbet i år')
                    ->default('0'),
            ])
            ->paginated([3, 4, 8, 12, 24, 36])
            ->recordUrl(
                fn(Model $record): string => UserResource::getUrl('index'),//getUrl('view', ['record' => $record]),
            );
    }

}
