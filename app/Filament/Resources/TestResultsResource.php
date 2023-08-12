<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestResultsResource\Pages;
use App\Models\TestResults;
use App\Models\Tests;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

//use App\Filament\Resources\TestResultsResource\RelationManagers;

class TestResultsResource extends Resource
{
    protected static ?string $model = TestResults::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Landslag';

    protected static ?string $modelLabel = 'Test resultat';

    protected static ?string $pluralModelLabel = 'Test resultater';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                DateTimePicker::make('dato')
                    ->required(),
                Select::make('testsID')
                    ->label('Hvilken test')
                    ->options(function () {

                        return tests::all()->pluck('navn', 'id');
                    }),

                Repeater::make('resultat')
                    ->label('Resultater')
                    ->schema(function (\Filament\Forms\Get $get): array {

                        $schema = [];
                        $test = $get('testsID') == 0 ? 1 : $get('testsID');
                        $data = tests::where('id', '=', $test)->get();

                        foreach ($data[0]['ovelser'] as $o) {
                            if ($o['type'] == 'tid' || $o['type'] == 'kg') {
                                $schema[] = TextInput::make($o['navn'])
                                    // ->mask(fn (TextInput\Mask $mask) => $mask->pattern('0[00].[00]'))
                                    ->required();
                            } else {
                                $schema[] = TextInput::make($o['navn'])
                                    ->required();
                            }
                        }

                        return $schema;
                    })
                    ->columns(3),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {

        return $table
            ->columns([
                TextColumn::make('dato')
                    ->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('tests.navn')->label('Test'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                ForceDeleteBulkAction::make(),
                RestoreBulkAction::make(),
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
