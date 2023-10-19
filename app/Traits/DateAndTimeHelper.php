<?php

namespace App\Traits;

use App\Models\Timesheet;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

trait DateAndTimeHelper
{

    public const FRA_DATO_TIME = 'fra_dato_time';
    public const TIL_DATO_TIME = 'til_dato_time';
    public const FRA_DATO_DATE = 'fra_dato_date';
    public const TIL_DATO_DATE = 'til_dato_date';

    /**
     * Retrieves the common fields for a specific user role.
     *
     * @param  bool  $isAdmin  Indicates whether the user is an admin or not.
     * @return array An array containing the common fields.
     */
    public static function getCommonFields(bool $isAdmin): array
    {
        // Initialize an empty array to store the common fields
        $fields = [];

        // Configuration for the date time picker
        $dateTimePickerConfig = [
            'hidden'  => fn($get) => $get('allDay'), // Callback function to determine if the picker is hidden
            'isAdmin' => $isAdmin, // Indicates whether the user is an admin or not
        ];

        // Configuration for the date picker
        $datePickerConfig = [
            'hidden'  => fn($get) => !$get('allDay'), // Callback function to determine if the picker is hidden
            'isAdmin' => $isAdmin, // Indicates whether the user is an admin or not
        ];

        if ($isAdmin) {
            $richEditorLabel = 'Beskrivelse';
        } else {
            $richEditorLabel = 'Begrunnelse (Valgfritt)';
        }

        // Merge the common fields with the dynamic fields
        return array_merge(
            $fields,
            self::dynamicDateTimePicker(self::FRA_DATO_TIME, 'Fra dato', $dateTimePickerConfig),
            self::dynamicDateTimePicker(self::TIL_DATO_TIME, 'Til dato', $dateTimePickerConfig),
            self::dynamicDatePicker(self::FRA_DATO_DATE, 'Fra dato', $datePickerConfig),
            self::dynamicDatePicker(self::TIL_DATO_DATE, 'Til dato', $datePickerConfig),
            self::dynamicRichEditor('description', $richEditorLabel),
            self::dynamicHidden('totalt', 0),
            self::dynamicHidden('unavailable', true)
        );
    }

    /**
     * Generates a dynamic date time picker component.
     *
     * @param  string  $name  The name of the component.
     * @param  string  $label  The label for the component.
     * @param  array  $config  Additional configuration options for the component. Default is an empty array.
     * @return array The generated dynamic date time picker component.
     */
    public static function dynamicDateTimePicker(string $name, string $label, array $config = []): array
    {
        $component = DateTimePicker::make($name)
            ->label($label)
            ->timezone('Europe/Oslo')
            ->suffixIcon('heroicon-o-clock')
            ->native(false)
            ->disabledDates(function (Get $get) {
                $recordId = $get('id');
                return self::getAllDisabledDates($get('user_id'), $recordId) ?? [];
            })
            ->formatStateUsing(
                fn($state) => is_null($state)
                    ? Carbon::now()->minute(floor(Carbon::now()->minute / 15) * 15)->second(0)->format('Y-m-d H:i')
                    : $state
            )
            ->minDate(fn($operation) => ($operation == 'edit' || $config['isAdmin'])
                ? null
                : Carbon::parse(today())->format('d.m.Y H:i'))
            ->minutesStep(15)
            ->seconds(false)
            ->required()
            ->live();

        // If the component name is 'til_dato_time', add the afterOrEqual validation
        if ($name === self::TIL_DATO_TIME) {
            $component->afterOrEqual(self::FRA_DATO_TIME);
        }

        if ($name === self::TIL_DATO_TIME && $config['isAdmin']) {
            $component->afterStateUpdated(function (Set $set, ?string $state, Get $get) {
                $formattedTime = self::calculateFormattedTimeDifference($get(self::FRA_DATO_TIME), $state);
                $set('totalt', $formattedTime);
            });
        }

        if ($name === self::FRA_DATO_TIME)
        {
            $component->afterStateUpdated(function (Set $set, ?string $state, Get $get, $operation) use ($config)
            {
                // Parse the new state and the previous 'til_dato_time'
                $newFraDato = Carbon::parse($state);
                $existingTilDato = Carbon::parse($get(self::TIL_DATO_TIME));

                // Check if only the date part has changed
                if ($newFraDato->format('Y-m-d') !== $existingTilDato->format('Y-m-d'))
                {
                    // Only the date part has changed, so update the date part of 'til_dato_time' without changing the time part
                    $updatedTilDato = $existingTilDato->setDate($newFraDato->year, $newFraDato->month, $newFraDato->day);
                    $set(self::TIL_DATO_TIME, $updatedTilDato->format('Y-m-d H:i'));
                } elseif($operation == 'create')
                {
                    $set(self::TIL_DATO_TIME, $newFraDato->addHour()->format('Y-m-d H:i'));
                }else
                {
                    $totalt = $get('totalt');
                    [$hours, $minutes] = explode(':', $totalt);
                    $durationInMinutes = ($hours * 60) + $minutes;

                    // Set the updated 'til_dato_time' by adding the duration to the new 'fra_dato_time'
                    $updatedTilDato = $newFraDato->copy()->addMinutes($durationInMinutes);
                    $set(self::TIL_DATO_TIME, $updatedTilDato->format('Y-m-d H:i'));
                }

                // If isAdmin is true, also update 'totalt'
                if ($config['isAdmin'])
                {
                    $formattedTime = self::calculateFormattedTimeDifference($state, $get(self::TIL_DATO_TIME));
                    $set('totalt', $formattedTime);
                }
            });
        }

        self::applyComponentConfig($component, $config);

        return [$component];
    }

    /**
     * Generates a dynamic date picker component.
     *
     * @param  string  $name  The name of the date picker.
     * @param  string  $label  The label for the date picker.
     * @param  array  $config  The configuration options for the date picker. Default is an empty array.
     * @return array Returns an array containing the dynamic date picker component.
     */
    public static function dynamicDatePicker(string $name, string $label, array $config = []): array
    {
        $component = DatePicker::make($name)
            ->label($label)
            ->native(false)
            ->disabledDates(function (Get $get) {
                $recordId = $get('id');
                return self::getAllDisabledDates($get('user_id'), $recordId) ?? [];
            })
            ->suffixIcon('calendar')
            ->displayFormat('d.m.Y')
            ->minDate(fn($operation) => ($operation == 'edit' || $config['isAdmin'])
                ? null
                : Carbon::parse(today())->format('d.m.Y'))
            ->live()
            ->required();

        // If the component name is 'til_dato_date', add the afterOrEqual validation
        if ($name === self::TIL_DATO_DATE) {
            $component->afterOrEqual(self::FRA_DATO_DATE);
        }

        if ($name === self::FRA_DATO_DATE) {
            $component->afterStateUpdated(
                fn(Set $set, ?string $state) => $set(self::TIL_DATO_DATE, Carbon::parse($state)->format('Y-m-d'))
            );
        }

        self::applyComponentConfig($component, $config);

        return [$component];
    }

    private static function getAllDisabledDates($user_id, $recordId): array
    {
        $cacheKey = "disabled_dates:user_{$user_id}:record_{$recordId}";
        return Cache::tags(['timesheet'])->remember($cacheKey, now()->addMonth(), function () use ($user_id, $recordId) {
            $query = Timesheet::whereYear('fra_dato', Carbon::now()->year)
                ->where('user_id', '=', $user_id);

            if ($recordId) {
                $query->where('id', '<>', $recordId);
            }

            return $query->pluck('fra_dato')
                ->unique()
                ->map(function ($date) {
                    return $date->format('Y-m-d');
                })
                ->toArray();
        });
    }


    /**
     * Calculates the formatted time difference between two given timestamps.
     *
     * @param  string  $startTime  The starting timestamp.
     * @param  string  $endTime  The ending timestamp.
     * @return string The formatted time difference in the format hh:mm.
     */
    private static function calculateFormattedTimeDifference(string $startTime, string $endTime): string
    {
        $fromDate = Carbon::parse($startTime);
        $toDate   = Carbon::parse($endTime);
        $minutes  = $fromDate->diffInMinutes($toDate);

        // Format minutes to hh:mm
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    /**
     * Apply the given configuration options to a component.
     *
     * @param  object  $component  The component to apply the configuration to.
     * @param  array  $config  The configuration options to apply.
     * @return void
     * @throws None
     */
    private static function applyComponentConfig(object $component, array $config): void
    {
        if (isset($config['hidden'])) {
            $component->hidden($config['hidden']);
        }
        if (isset($config['minDate'])) {
            $component->minDate($config['minDate']);
        }
        if (isset($config['afterStateUpdated'])) {
            $component->afterStateUpdated($config['afterStateUpdated']);
        }
        if (isset($config['afterOrEqual'])) {
            $component->afterOrEqual($config['afterOrEqual']);
        }
    }

    /**
     * Generates a dynamic rich editor.
     *
     * @param  string  $name  The name of the editor.
     * @return array The generated rich editor configuration.
     */
    public static function dynamicRichEditor(string $name, string $richEditorLabel): array
    {
        return [
            RichEditor::make($name)
                ->label($richEditorLabel)
                ->disableToolbarButtons([
                    'attachFiles',
                    'blockquote',
                    'codeBlock',
                    'h2',
                    'h3',
                    'link',
                    'redo',
                    'strike',
                ])
                ->maxLength(255)
        ];
    }

    /**
     * Generates a dynamic hidden input field for a form.
     *
     * @param  string  $name  The name of the input field.
     * @param  mixed  $default  The default value of the input field.
     * @return array An array containing the generated hidden input field.
     */
    public static function dynamicHidden(string $name, mixed $default): array
    {
        return [
            Hidden::make($name)->default($default)
        ];
    }

    /**
     * Transforms the given form data for saving.
     *
     * @param  array  $data  The form data to be transformed.
     * @return array The transformed form data.
     */
    public static function transformFormDataForSave(array $data): array
    {
        if ($data['allDay']) {
            $data['fra_dato'] = $data[self::FRA_DATO_DATE];
            $data['til_dato'] = $data[self::TIL_DATO_DATE];
        } else {
            $data['fra_dato'] = $data[self::FRA_DATO_TIME];
            $data['til_dato'] = $data[self::TIL_DATO_TIME];
        }

        if ($data['totalt']) {
            $data['totalt'] = $data['unavailable'] ? 0 : self::calculateTotalMinutes($data['totalt']);
        } else {
            $data['totalt'] = self::calculateTotalMinutes(self::calculateFormattedTimeDifference($data['fra_dato'], $data['til_dato']));
        }

        return $data;
    }

    /**
     * Calculates the total number of minutes from a given time string.
     *
     * @param  string  $time  The time string in the format "hh:mm".
     * @return int The total number of minutes calculated from the time string.
     */
    private static function calculateTotalMinutes(string $time): int
    {
        if (preg_match('/(\d+):(\d+)/', $time, $matches)) {
            $hours   = intval($matches[1]);
            $minutes = intval($matches[2]);
            return $hours * 60 + $minutes;
        }

        return 0;
    }

    /**
     * Transforms the given data array for filling.
     *
     * @param  array  $data  The data array to transform
     * @return array The transformed data array
     */
    public static function transformFormDataForFill(array $data): array
    {
        if ($data['allDay']) {
            $data[self::FRA_DATO_DATE] = $data['fra_dato'];
            $data[self::TIL_DATO_DATE] = $data['til_dato'];
            $data['totalt']            = 0;
        } else {
            $timezone = 'Europe/Oslo';

            $fra_dato = Carbon::parse($data['fra_dato'])->setTimezone($timezone)->format('Y-m-d H:i');
            $til_dato = Carbon::parse($data['til_dato'])->setTimezone($timezone)->format('Y-m-d H:i');

            $minutes        = $data['totalt'];
            $formattedTime  = sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
            $data['totalt'] = $formattedTime;

            $data[self::FRA_DATO_TIME] = $fra_dato;
            $data[self::TIL_DATO_TIME] = $til_dato;
        }

        return $data;
    }

}
