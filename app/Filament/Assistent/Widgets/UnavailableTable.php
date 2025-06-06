<?php

namespace App\Filament\Assistent\Widgets;

use App\Models\Timesheet;
use App\Models\User;
use App\Traits\DateAndTimeHelper;
use App\Transformers\FormDataTransformer;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class UnavailableTable extends BaseWidget
{
    use DateAndTimeHelper;

    protected static ?string $heading = 'Tider satt som ikke tilgjengelig';

    protected static ?string $pollingInterval = null;

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Timesheet::query()->where('user_id', Auth::id())->where('unavailable', '=', 1)->inFuture('til_dato')
            )
            ->columns([
                TextColumn::make('fra_dato')->dateTime('d.m.Y, H:i')->sortable(),
                TextColumn::make('til_dato')->dateTime('d.m.Y, H:i')->sortable(),
            ])
            ->defaultSort('fra_dato')
            ->headerActions([
                CreateAction::make()
                    ->label('Legg til tid du ikke kan jobbe')
                    ->mutateFormDataUsing(function (array $data): array {
                        return FormDataTransformer::transformFormDataForSave($data);
                    })
                    ->form([
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
                                Hidden::make('user_id')->default(Auth::user()->id),
                            ])->columns(),
                    ]),
            ])
            ->emptyStateHeading('Ingen tider registrert.')
            ->emptyStateDescription('')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Legg til tid du ikke kan jobbe')
                    ->mutateFormDataUsing(function (array $data): array {
                        return FormDataTransformer::transformFormDataForSave($data);
                    })
                    ->after(function () {
                        $recipient = User::query()->role('admin')->get();

                        Notification::make()
                            ->title(auth()->user()->name.' Har lagt til en tid han/hun ikke kan jobbe.')
                            ->actions([
                                Action::make('view')
                                    ->url((route('filament.admin.resources.timelister.index', [
                                        'tableFilters' => [
                                            'Ikke tilgjengelig' => [
                                                'isActive' => true,
                                            ],
                                            'assistent' => [
                                                'value' => auth()->user()->id,
                                            ],
                                        ],
                                    ])))
                                    ->button(),
                            ])
                            ->sendToDatabase($recipient);
                    })
                    ->form([
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
                                Hidden::make('user_id')->default(Auth::user()->id),
                            ])->columns(),
                    ]),
            ]);
    }
}
