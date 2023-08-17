<?php

namespace App\Filament\Assistent\Resources;

use App\Filament\Assistent\Resources\TimesheetResource\Pages;
use App\Filament\Assistent\Resources\TimesheetResource\RelationManagers;
use App\Models\Timesheet;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Alle timer';

    protected static ?string $label = 'Time';

    protected static ?string $pluralLabel = 'Timer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Timesheet::query()->where('user_id', Auth::user()->id)->orderByDesc('fra_dato'))
            ->columns([
                Tables\Columns\TextColumn::make('fra_dato')->dateTime('d.m.Y, H:i')->sortable(),
                Tables\Columns\TextColumn::make('til_dato')->dateTime('d.m.Y, H:i')->sortable(),
                Tables\Columns\TextColumn::make('totalt')
                    ->formatStateUsing(fn(string $state): string => (new \App\Services\UserStatsService)
                        ->minutesToTime($state))
                    ->summarize(Sum::make()
                        ->formatStateUsing(fn(
                            string $state
                        ): string => (new \App\Services\UserStatsService)->minutesToTime($state))),
                Tables\Columns\IconColumn::make('unavailable')->label('Satt som borte?')->boolean(),
                Tables\Columns\IconColumn::make('allDay')->label('Hele dagen?')->boolean(),
            ])->striped()->emptyStateHeading('Ingen registrerte timer enda')
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
//                Tables\Actions\BulkActionGroup::make([
//                    Tables\Actions\DeleteBulkAction::make(),
//                ]),
            ])
            ->emptyStateActions([
                //Tables\Actions\CreateAction::make(),
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
            'index'  => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            'edit'   => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}
