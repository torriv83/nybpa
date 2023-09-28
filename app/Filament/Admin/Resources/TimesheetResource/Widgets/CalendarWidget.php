<?php

namespace App\Filament\Admin\Resources\TimesheetResource\Widgets;

use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{

    public Model | string | null $model = Timesheet::class;

    public function getFormSchema(): array
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
            DateTimePicker::make('fra_dato')
                ->label('Starter')
                ->displayFormat('d.m.Y H:i')
                ->minutesStep(15)
                ->required(),
            DateTimePicker::make('til_dato')
                ->label('Slutter')
                ->displayFormat('d.m.Y H:i')
                ->minutesStep(15)
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function ($set, $get, $state) {
                    $set('tot', Carbon::parse($get('til_dato'))->diff(Carbon::parse($get('fra_dato')))->format('%H:%I'));
                    $set('totalt', Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($get('fra_dato')))->diffInMinutes(Carbon::parse($state)));
                }),
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
            Placeholder::make('tot')
                ->label('Totalt')
                ->content(function (Get $get) {
                    if($get('fra_dato') == null || $get('til_dato') == null){
                        return 0;
                    }
                    return Carbon::parse($get('til_dato'))->diff(Carbon::parse($get('fra_dato')))->format('%H:%I');
                }),
            Hidden::make('totalt'),
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

             $color          = $item->unavailable ? 'rgba(255, 0, 0, 0.2)' : '';
             $item->til_dato = $item->allDay ? Carbon::parse($item->til_dato)->addDay() : $item->til_dato;
             return [
                 'id'              => $item->id,
                 'title'           => Str::limit($item->user->name, 15),
                 'start'           => $item->fra_dato,
                 'end'             => $item->til_dato,
                 'allDay'          => $item->allDay,
                 'description'     => $item->description,
                 'heleDagen'       => $item->allDay,
                 'assistentID'     => $item->user_id,
                 'unavailable'     => $item->unavailable,
                 'totalt'          => $item->totalt,
                 'backgroundColor' => $color,
                 'borderColor'     => $color,
             ];
         })->toArray();
     }

    /**
     * Triggered when dragging stops and the event has moved to a different day/time.
     */
    public function onEventDrop(array $event, array $oldEvent, array $relatedEvents, array $delta): bool
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
                        $form->fill([
                            'allDay' => $arguments['allDay'] ?? false,
                            'fra_dato' => $arguments['start'] ?? null,
                            'til_dato' => $arguments['end'] ?? null
                        ]);
                    }
                )
        ];
    }

    /**
     * Triggered when event's resize stops.
     */
    public function onEventResize(array $event, array $oldEvent, array $relatedEvents, array $startDelta, array $endDelta): bool
    {
        $this->eventUpdate($event);

        return false;
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
        }

        Notification::make()
            ->title('Tid endret')
            ->success()
            ->send();
    }
}
