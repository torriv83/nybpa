<?php

namespace App\Filament\Admin\Resources\TimesheetResource\Widgets;

use App\Models\Timesheet;
use App\Models\User;
use App\Traits\DateAndTimeHelper;
use App\Transformers\FormDataTransformer;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Saade\FilamentFullCalendar\Actions;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Actions\DeleteAction;
use Saade\FilamentFullCalendar\Actions\EditAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    use DateAndTimeHelper;

    public Model|string|null $model = Timesheet::class;

    public function getFormSchema(): array
    {
        return [
            // Section
            Section::make('Assistent')
                ->description('Velg assistent og om han/hun er tilgjengelig eller ikke, og om det gjelder hele dagen.')
                ->schema([
                    Select::make('user_id')
                        ->label('Hvem')
                        ->options(User::query()->assistenter()->pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->columnSpan(2),

                    Checkbox::make('unavailable')
                        ->label('Ikke Tilgjengelig?'),

                    Checkbox::make('allDay')
                        ->label('Hele dagen?')->live(),

                ])->columns(),

            // Section
            Section::make('Tid')
                ->description('Velg fra og til')
                ->schema([
                    ...self::getCommonFields(true),
                    TextInput::make('totalt')
                        ->label('Total tid')
                        ->disabled()
                        ->dehydrated(),
                ])->columns(),
        ];
    }

    public function refreshRecords(): void
    {
        parent::refreshRecords();
        Cache::tags(['timesheet'])->flush();
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        $schedules = Cache::tags(['timesheet'])->remember('schedules', now()->addDay(), function () {
            return Timesheet::query()->with('user')->get();
        });

        return $schedules->map(function ($item) {
            $color = $item->unavailable ? 'rgba(255, 0, 0, 0.2)' : '';
            $item->til_dato = $item->allDay ? Carbon::parse($item->til_dato)->addDay() : $item->til_dato;

            return [
                'id' => $item->id,
                'title' => Str::limit($item->user->name, 15),
                'start' => $item->fra_dato,
                'end' => $item->til_dato,
                'allDay' => $item->allDay,
                'description' => $item->description,
                'heleDagen' => $item->allDay,
                'assistentID' => $item->user_id,
                'unavailable' => $item->unavailable,
                'totalt' => $item->totalt,
                'backgroundColor' => $color,
                'borderColor' => $color,
            ];
        })->toArray();
    }

    /**
     * Triggered when dragging stops and the event has moved to a different day/time.
     */
    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta, ?array $oldResource, ?array $newResource): bool
    {
        $this->eventUpdate($event);

        return false;
    }

    public function eventUpdate($event): void
    {
        $slutter = $event['extendedProps']['heleDagen'] ? Carbon::parse($event['end'])->subDay() : $event['end'];

        $tid = Timesheet::find($event['id']);
        $tid->fra_dato = $event['start'];
        $tid->til_dato = $slutter;
        $tid->description = $event['extendedProps']['description'];
        $tid->user_id = $event['extendedProps']['assistentID'];
        $tid->allDay = $event['extendedProps']['heleDagen'];
        $tid->totalt = Carbon::parse($event['start'])->diffInMinutes($event['end']);

        if ($tid->save()) {
            Cache::tags(['timesheet'])->flush();
        }

        Notification::make()
            ->title('Tid endret')
            ->success()
            ->send();
    }

    /**
     * Triggered when event's resize stops.
     */
    public function onEventResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool
    {
        $this->eventUpdate($event);

        return false;
    }

    protected function headerActions(): array
    {
        return [
            CreateAction::make()
                ->mountUsing(
                    function (Form $form, array $arguments) {
                        if ($arguments) {
                            if ($arguments['allDay'] ?? false) {
                                $form->fill([
                                    'allDay' => $arguments['allDay'],
                                    'fra_dato_date' => $arguments['start'] ?? null,
                                    'til_dato_date' => $arguments['end'] ?? null,
                                ]);
                            } else {
                                $form->fill([
                                    'allDay' => $arguments['allDay'] ?? false,
                                    'fra_dato_time' => $arguments['start'] ?? null,
                                    'til_dato_time' => $arguments['end'] ?? null,
                                ]);
                            }
                        } else {
                            $form->fill([
                                'allDay' => false,
                                'fra_dato_time' => null,
                                'til_dato_time' => null,
                            ]);
                        }
                    }
                )
                ->mutateFormDataUsing(
                    function (array $data): array {
                        return FormDataTransformer::transformFormDataForSave($data);
                    }
                ),
        ];
    }

    protected function modalActions(): array
    {
        return [
            EditAction::make()
                ->mutateFormDataUsing(
                    function (array $data): array {
                        return FormDataTransformer::transformFormDataForSave($data);
                    }
                )
                ->mountUsing(
                    function ($record, Form $form) {
                        $form->fill(FormDataTransformer::transformFormDataForFill($record->toArray()));
                    }
                )->after(function () {
                    $this->refreshRecords();
                }),
            DeleteAction::make(),
        ];
    }

    protected function viewAction(): Actions\ViewAction
    {
        return Actions\ViewAction::make()->mountUsing(
            function ($record, Form $form) {
                $form->fill(FormDataTransformer::transformFormDataForFill($record->toArray()));
            }
        );
    }
}
