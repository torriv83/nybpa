<?php
/**
 * Created by ${USER}.
 * Date: 18.04.2023
 * Time: 06.48
 * Company: Rivera Consulting
 */

namespace App\Filament\Resources;

use App\Filament\Resources\DayResource\Pages;
use App\Models\Day;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;

class DayResource extends Resource
{
    protected static ?string $model = Day::class;

    protected static ?string $slug = 'days';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Day $record): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Day $record): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListDays::route('/'),
            'create' => Pages\CreateDay::route('/create'),
            'edit'   => Pages\EditDay::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
