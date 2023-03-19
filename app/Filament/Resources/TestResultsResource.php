<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use App\Models\Tests;
use App\Models\TestResults;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TestResultsResource\Pages;
use App\Filament\Resources\TestResultsResource\RelationManagers;

class TestResultsResource extends Resource
{
    protected static ?string $model = TestResults::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup  = 'Landslag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('dato')
                    ->required(),
                Forms\Components\Select::make('testsID')
                    ->label('Hvilken test')
                    ->options(function () {
                        return tests::all()->pluck('navn', 'id');
                    }),

                Forms\Components\Repeater::make('resultat')
                    ->label('Resultater')
                    ->schema(function (Closure $get): array {
                        $schema = [];
                        $test = $get('testsID') == 0 ? 1 : $get('testsID');
                        $data = tests::where('id', '=', $test)->get();
                        foreach ($data[0]['ovelser'] as $o) {
                            if ($o['type'] == 'tid' || $o['type'] == 'kg') {
                                $schema[] = TextInput::make($o['navn'])
                                    ->mask(fn (TextInput\Mask $mask) => $mask->pattern('0[00].[00]'))
                                    ->required();
                            } else {
                                $schema[] = TextInput::make($o['navn'])
                                    ->required();
                            }
                        }

                        return $schema;
                    })
                    ->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('dato')
                    ->dateTime('d.m.Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('tests.navn')->label('Test'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestResults::route('/'),
            'create' => Pages\CreateTestResults::route('/create'),
            'edit' => Pages\EditTestResults::route('/{record}/edit'),
            // 'view' => Pages\ViewTestResults::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
