<?php

namespace App\Filament\Landslag\Resources;

use App\Filament\Landslag\Resources\TestResultsResource\Pages\CreateTestResults;
use App\Filament\Landslag\Resources\TestResultsResource\Pages\EditTestResults;
use App\Filament\Landslag\Resources\TestResultsResource\Pages\ListTestResults;
use App\Models\TestResults;
use App\Models\Tests;
use Exception;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

// use App\Filament\Resources\TestResultsResource\RelationManagers;

class TestResultsResource extends Resource
{
    protected static ?string $model = TestResults::class;

    protected static ?string $navigationIcon = 'icon-graph';

    protected static ?string $navigationGroup = 'Tester';

    protected static ?string $modelLabel = 'Test resultat';

    protected static ?string $pluralModelLabel = 'Test resultater';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('hvilken_test')
                        ->label('Hvilken test?')
                        ->description('Velg hvilken test du skal loggføre')
                        ->schema([
                            Select::make('tests_id')
                                ->options(function () {
                                    return tests::pluck('navn', 'id');
                                })->label('Test')->reactive(),
                            DateTimePicker::make('dato')->seconds(false),
                        ]),

                    Step::make('Resultater')
                        ->description('Legg inn resultater fra testen her')
                        ->schema([
                            Repeater::make('resultat')
                                ->schema(function (Get $get): array {
                                    $schema = [];
                                    if ($get('tests_id')) {
                                        $data = tests::where('id', '=', $get('tests_id'))->get();
                                        foreach ($data[0]['ovelser'] as $o) {
                                            if ($o['type'] == 'tid' || $o['type'] == 'kg') {
                                                $schema[] = TextInput::make($o['navn'])
                                                    ->regex('/^\d{1,3}(\.\d{1,2})?$/')
                                                    // ->mask(fn (TextInput\Mask $mask) => $mask->pattern('0[00].[00]'))
                                                    ->required()->placeholder('00.00');
                                            } else {
                                                $schema[] = TextInput::make($o['navn'])
                                                    ->required();
                                            }
                                        }
                                    }

                                    return $schema;
                                }),
                            Hidden::make('tests_id'),
                        ]),
                ]),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dato')
                    ->dateTime('d.m.Y H:i')->sortable(),
                TextColumn::make('tests.navn')->label('Test'),
                ViewColumn::make('resultat')->label('Resultat')->view('filament.resources.testresult-resource.results-type-column'),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('Test')->relationship('tests', 'navn'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()->label('Arkiver'),
                ForceDeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ])->defaultSort('dato', 'desc');
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
            'index' => ListTestResults::route('/'),
            'create' => CreateTestResults::route('/create'),
            'edit' => EditTestResults::route('/{record}/edit'),
            // 'view' => Pages\ViewTestResults::route('/{record}'),
        ];
    }
}
