<?php

namespace App\Filament\Widgets;

use Closure;
use App\Models\User;
use Filament\Tables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;

class Ansatte extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static ?string $heading = 'Ansatte';
    protected static ?int $sort = 7;
    protected array|string|int $columnSpan = 6;

    public function getTableRecordKey(Model $record): string
    {
        return uniqid();
    }
    
    protected function getTableQuery(): Builder
    {
        return User::query()->role(['Fast ansatt', 'Tilkalling']);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Navn'),
            Tables\Columns\BadgeColumn::make('roles.name')
                ->label('Stilling')
                ->colors([
                    'success',
                    'primary' => 'Tilkalling',
                ]),
            Tables\Columns\TextColumn::make('email')
                ->label('E-post'),
            Tables\Columns\TextColumn::make('phone')
                ->label('Telefon')
                ->default('12345678'),
        ];
    }
}