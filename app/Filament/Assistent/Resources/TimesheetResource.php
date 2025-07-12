<?php

namespace App\Filament\Assistent\Resources;

use App\Filament\Assistent\Resources\TimesheetResource\Pages;
use App\Models\Timesheet;
use App\Services\UserStatsService;
use App\Traits\DateAndTimeHelper;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class TimesheetResource extends Resource
{
    use DateAndTimeHelper;

    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Alle timer';

    protected static ?string $label = 'Time';

    protected static ?string $pluralLabel = 'Timer';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Seksjon
                Section::make(fn () => Auth::user()->name)
                    ->description('Velg om det gjelder hele dagen eller ikke')
                    ->schema([
                        Checkbox::make('allDay')
                            ->label('Hele dagen?')->live(),
                    ])->columns(),

                Section::make('Tid')
                    ->schema([
                        ...self::getCommonFields(false),
                        Hidden::make('totalt')->default(0),
                        Hidden::make('unavailable')->default(true),
                        Hidden::make('user_id')->default(Auth::user()->id)->live(),
                    ])->columns(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Timesheet::query()->where('user_id', Auth::user()->id)->orderByDesc('fra_dato'))
            ->columns([
                Tables\Columns\TextColumn::make('fra_dato')
                    ->dateTime('d.m.Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('til_dato')
                    ->dateTime('d.m.Y, H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('totalt')
                    ->getStateUsing(function (Timesheet $record) {
                        if ($record->unavailable) {
                            return '-';
                        } else {
                            $minutes = $record->totalt;

                            return sprintf('%02d', intdiv($minutes, 60)).':'.(sprintf('%02d', $minutes % 60));
                        }
                    })
                    ->summarize(Sum::make()
                        ->formatStateUsing(fn (
                            int $state
                        ): string => (new UserStatsService)->minutesToTime($state))),
                Tables\Columns\IconColumn::make('unavailable')
                    ->label('Satt som borte?')
                    ->boolean(),
                Tables\Columns\IconColumn::make('allDay')
                    ->label('Hele dagen?')
                    ->boolean(),
            ])
            ->striped()
            ->emptyStateHeading('Ingen registrerte timer enda')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(function ($record) {
                        return $record->unavailable != 1;
                    })
                    ->before(function ($record) {
                        if ($record->allDay) {
                            $record->fra_dato = $record->fra_dato_date;
                        } else {
                            $record->fra_dato = $record->fra_dato_time;
                        }
                    }),
                Tables\Actions\DeleteAction::make()
                    ->hidden(function ($record) {
                        return $record->unavailable != 1;
                    }),
            ])
            ->bulkActions([
                //                Tables\Actions\BulkActionGroup::make([
                //                    Tables\Actions\DeleteBulkAction::make(),
                //                ]),
            ])
            ->emptyStateActions([
                // Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            'edit' => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}
