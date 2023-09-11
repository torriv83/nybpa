<?php

namespace App\Filament\Resources\TimesheetResource\Widgets;

use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    public function getCreateEventModalSubmitButtonLabel(): string
    {
        return 'Lagre';
    }

    public function getCreateEventModalCloseButtonLabel(): string
    {
        return 'Lukk';
    }

    public function getEditEventModalSubmitButtonLabel(): string
    {
        return 'Lagre';
    }

    public function getEditEventModalCloseButtonLabel(): string
    {
        return $this->editEventForm->isDisabled()
            ? 'Lukk'
            : 'Avbryt';
    }

    public function getCreateEventModalTitle(): string
    {
        return 'Opprett ny tid';
    }

    protected $listeners = [
        'deleteEvent'           => 'deleteEvent',
        'refreshEventsCalendar' => 'refreshEventsCalendar',
    ];

    public function getViewData(): array
    {

        $schedules = Cache::tags(['timesheet'])->remember('schedules', now()->addDay(), function () {
            return Timesheet::query()->with('user')->get();
        });

        return $schedules->map(function ($item) {

            $farge          = $item->unavailable ? 'rgba(255, 0, 0, 0.2)' : '';
            $item->til_dato = $item->allDay ? Carbon::parse($item->til_dato)->addDay() : $item->til_dato;

            return [
                'id'              => $item->id,
                'title'           => $item->user->name,
                'start'           => $item->fra_dato,
                'end'             => $item->til_dato,
                'allDay'          => $item->allDay,
                'description'     => $item->description,
                'heleDagen'       => $item->allDay,
                'assistentID'     => $item->user_id,
                'unavailable'     => $item->unavailable,
                'backgroundColor' => $farge,
                'borderColor'     => $farge,
            ];
        })->toArray();

    }

    public function deleteEvent(): void
    {
        $this->event->delete();

        $this->dispatch('close-modal', ['id' => 'fullcalendar--edit-event-modal']);

        Cache::tags(['timesheet'])->flush();
        $this->refreshEvents();

        Notification::make()
            ->title('Tiden er slettet')
            ->success()
            ->send();
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
//     public function fetchEvents(array $fetchInfo): array
//     {
//
//         // You can use $fetchInfo to filter events by date.
//         $schedules = Timesheet::query()
//             ->where([
//                 ['fra_dato', '>=', $fetchInfo['start']],
//                 ['til_dato', '<', $fetchInfo['end']],
//             ])
//             ->get();
//
//         $data = $schedules->map(function ($item, $key) {
//
//             $farge = $item->unavailable ? '#ff0000' : '';
//             // $display = $item->unavailable ? 'background' : '';
//             $item->til_dato = $item->allDay ? Carbon::parse($item->til_dato)->addDay() : $item->til_dato;
//
//             return [
//                 'id'              => $item->id,
//                 'title'           => $item->user->name,
//                 'start'           => $item->fra_dato,
//                 'end'             => $item->til_dato,
//                 'allDay'          => $item->allDay,
//                 'description'     => $item->description,
//                 'heleDagen'       => $item->allDay,
//                 'assistentID'     => $item->user_id,
//                 'unavailable'     => $item->unavailable,
//                 'backgroundColor' => $farge,
//                 'borderColor'     => $farge,
//                 // 'display' => $display,
//             ];
//         });
//
//         // $data = $data['eventBorderColor'][] = '#000000';
//
//         return $data->toArray();
//     }

    protected static function getCreateEventFormSchema(): array
    {
        return [
            Select::make('user_id')
                ->label('Assistent')
                ->options(User::role(['Fast ansatt', 'Tilkalling'])->pluck('name', 'id'))
                ->required(),
            Grid::make()
                ->schema([
                    Checkbox::make('allDay')
                        ->label('Hele dagen?'),
                    Checkbox::make('unavailable')
                        ->label('Settes som borte?'),
                ]),
            DateTimePicker::make('start')
                ->label('Starter')
                ->displayFormat('d.m.Y H:i')
                ->minutesStep(15)
                ->required(),
            DateTimePicker::make('end')
                ->label('Slutter')
                ->displayFormat('d.m.Y H:i')
                ->minutesStep(15)
                ->required(),
            RichEditor::make('description')
                ->label('Beskrivelse')
                ->disableToolbarButtons([
                    'attachFiles',
                    'blockquote',
                    'codeBlock',
                    'h2',
                    'h3',
                    'link',
                    'redo',
                    'strike',
                ]),
        ];
    }

    protected static function getEditEventFormSchema(): array
    {
        return [

            TextInput::make('title')->label('Assistent')
                ->disabled(),
            Grid::make()
                ->schema([
                    Checkbox::make('extendedProps.heleDagen')
                        ->label('Hele dagen?'),
                    Checkbox::make('extendedProps.unavailable')
                        ->label('Settes som borte?'),
                ]),
            DateTimePicker::make('start')
                ->label('Starter')
                ->displayFormat('d.m.Y H:i')
                ->minutesStep(15)
                ->required(),
            DateTimePicker::make('end')
                ->label('Slutter')
                ->afterStateHydrated(function (\Filament\Forms\Set $set, $state, $get) {
                    $allDay = $get('extendedProps.heleDagen');

                    if ($allDay) {
                        $set('end', Carbon::parse($state)->subDay());
                    }
                })
                ->displayFormat('d.m.Y H:i')
                ->minutesStep(15),
            RichEditor::make('extendedProps.description')
                ->label('Beskrivelse')
                ->disableToolbarButtons([
                    'attachFiles',
                    'blockquote',
                    'codeBlock',
                    'h2',
                    'h3',
                    'link',
                    'redo',
                    'strike',
                ]),
            Hidden::make('id'),
            Hidden::make('extendedProps.assistentID'),

        ];
    }

    public function createEvent(array $data): void
    {

        // Create the event with the provided $data.
        Timesheet::create([
            'fra_dato'    => $data['start'],
            'til_dato'    => $data['end'],
            'user_id'     => $data['user_id'],
            'unavailable' => $data['unavailable'],
            'allDay'      => $data['allDay'],
            'description' => $data['description'],
            'totalt'      => Carbon::createFromFormat('Y-m-d H:i:s', $data['start'])->diffInMinutes($data['end']),
        ]);

        Cache::tags(['timesheet'])->flush();

        $this->refreshEvents();

        Notification::make()
            ->title('Tid lagret')
            ->success()
            ->send();
    }

    public function editEvent(array $data): void
    {

        $tid = Timesheet::find($data['id']);

        $tid->fra_dato    = $data['start'];
        $tid->til_dato    = $data['end'];
        $tid->description = $data['extendedProps']['description'];
        $tid->user_id     = $data['extendedProps']['assistentID'];
        $tid->unavailable = $data['extendedProps']['unavailable'];
        $tid->allDay      = $data['extendedProps']['heleDagen'];
        $tid->totalt      = Carbon::createFromFormat('Y-m-d H:i:s', $data['start'])->diffInMinutes($data['end']);

        if ($tid->save()) {

            Cache::tags(['timesheet'])->flush();

            $this->refreshEvents();

            Notification::make()
                ->title('Tid endret')
                ->success()
                ->send();
        }

    }

    // Resolve Event record into Model property
    public function resolveEventRecord(array $data): Model
    {
        // Using Appointment class as example
        return Timesheet::find($data['id']);
    }

    /**
     * Triggered when the user clicks an event.
     */
    public function onEventClick($event): void
    {
        parent::onEventClick($event);
    }

    /**
     * Triggered when dragging stops and the event has moved to a different day/time.
     */
    public function onEventDrop($newEvent): void
    {
        $this->eventUpdate($newEvent);
        Cache::tags(['timesheet'])->flush();
    }

    /**
     * Triggered when event's resize stops.
     */
    public function onEventResize($event): void
    {
        $this->eventUpdate($event);

        Notification::make()
            ->title('Tid endret')
            ->success()
            ->send();

        Cache::tags(['timesheet'])->flush();
    }

    public function eventUpdate($event): void
    {

        $slutter = $event['extendedProps']['heleDagen'] ? Carbon::parse($event['end'])->subDay() : $event['end'];

        $tid              = Timesheet::find($event['id']);
        $tid->fra_dato    = $event['start'];
        $tid->til_dato    = $slutter;
        $tid->description = $event['extendedProps']['description'];
        $tid->user_id     = $event['extendedProps']['assistentID'];
        $tid->allDay      = $event['extendedProps']['heleDagen'];
        $tid->totalt      = Carbon::parse($event['start'])->diffInMinutes($event['end']);

        if ($tid->save()) {
            Cache::tags(['timesheet'])->flush();
            $this->refreshEvents();
        }

        Notification::make()
            ->title('Tid endret')
            ->success()
            ->send();
    }
}
