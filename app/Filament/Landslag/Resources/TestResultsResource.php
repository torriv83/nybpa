<?php

namespace App\Filament\Landslag\Resources;

use App\Filament\Landslag\Resources\TestResultsResource\Pages\CreateTestResults;
use App\Filament\Landslag\Resources\TestResultsResource\Pages\EditTestResults;
use App\Filament\Landslag\Resources\TestResultsResource\Pages\ListTestResults;
use App\Models\TestResults;
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

//use App\Filament\Resources\TestResultsResource\RelationManagers;

class TestResultsResource extends Resource
{
    protected static ?string $model = TestResults::class;

    protected static ?string $navigationIcon = 'icon-graph';

    protected static ?string $navigationGroup = 'Tester';

    protected static ?string $modelLabel = 'Test resultat';

    protected static ?string $pluralModelLabel = 'Test resultater';


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
                ViewColumn::make('resultat')->label('Resultat')->view('filament.resources.testresult-resource.results-type-column'),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('Test')->relationship('tests', 'navn')
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
            'index'  => ListTestResults::route('/'),
            'create' => CreateTestResults::route('/create'),
            'edit'   => EditTestResults::route('/{record}/edit'),
            // 'view' => Pages\ViewTestResults::route('/{record}'),
        ];
    }
}
