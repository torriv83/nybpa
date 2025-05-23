<?php

/**
 * Created by Tor Rivera.
 * Date: 20.10.2023
 * Time: 03.08
 * Company: Rivera Consulting
 */

namespace App\Generators;

use App\Constants\Timesheet;
use App\Services\DateTimeService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Carbon;

class FormComponentGenerator
{
    protected DateTimeService $dateTimeService;

    public function __construct(DateTimeService $dateTimeService)
    {
        $this->dateTimeService = $dateTimeService;
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
        // Create a new instance of the DateTimePicker component
        $component = DateTimePicker::make($name)
            ->label($label)
            ->timezone('Europe/Oslo')
            ->suffixIcon('heroicon-o-clock')
            ->native(false)
            ->disabledDates(function (Get $get, dateTimeService $dateTimeService) {
                // Get the record ID from the request parameters
                $recordId = $get('id');

                // Get the list of disabled dates for the user and record
                return $dateTimeService->getAllDisabledDates($get('user_id'), $recordId);
            })
            ->formatStateUsing(
                fn ($state) => is_null($state)
                    ? Carbon::now()->minute(floor(Carbon::now()->minute / 15) * 15)->second(0)->format('Y-m-d H:i')
                    : $state
            )
            ->minDate(fn ($operation) => ($operation == 'edit' || $config['isAdmin'])
                ? null
                : Carbon::parse(today())->format('d.m.Y H:i'))
            ->minutesStep(Timesheet::MINUTES_STEP)
            ->seconds(false)
            ->required()
            ->live();

        // If the component name is 'til_dato_time', add the afterOrEqual validation
        if ($name === Timesheet::TIL_DATO_TIME) {
            $component->afterOrEqual(Timesheet::FRA_DATO_TIME);
        }

        // If the component name is 'til_dato_time' and the user is an admin, update the 'totalt' component state
        if ($name === Timesheet::TIL_DATO_TIME && $config['isAdmin']) {
            $component->afterStateUpdated(function (Set $set, ?string $state, Get $get, dateTimeService $dateTimeService) {
                // Calculate the formatted time difference between 'FRA_DATO_TIME' and the current state
                $formattedTime = $dateTimeService->calculateFormattedTimeDifference($get(Timesheet::FRA_DATO_TIME), $state);

                // Set the 'totalt' component state to the calculated formatted time
                $set('totalt', $formattedTime);
            });
        }

        // If the component name is 'fra_dato_time', update the 'til_dato_time' component state
        if ($name === Timesheet::FRA_DATO_TIME) {
            $component->afterStateUpdated(function (Set $set, ?string $state, Get $get, $operation) use ($config) {
                // Update the 'til_dato_time' component state based on the new 'fra_dato_time' state
                DateTimeService::updateTilDatoTime($state, $get, $set, $operation, $config['isAdmin']);
            });
        }

        // Apply additional configuration options to the component
        self::applyComponentConfig($component, $config);

        // Return the generated dynamic date time picker component
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
        // Create a new DatePicker component with the given name
        $component = DatePicker::make($name)
            ->label($label)
            ->native(false)
            ->disabledDates(function (Get $get, DateTimeService $dateTimeService) {
                // Get the id from the request parameters
                $recordId = $get('id');

                // Get all disabled dates based on the user id and record id
                return $dateTimeService->getAllDisabledDates($get('user_id'), $recordId);
            })
            ->suffixIcon('calendar')
            ->displayFormat('d.m.Y')
            ->minDate(fn ($operation) => ($operation == 'edit' || $config['isAdmin'])
                ? null
                : Carbon::parse(today())->format('d.m.Y'))
            ->live()
            ->required();

        // If the component name is 'til_dato_date', add the afterOrEqual validation
        if ($name === Timesheet::TIL_DATO_DATE) {
            $component->afterOrEqual(Timesheet::FRA_DATO_DATE);
        }

        // If the component name is 'fra_dato_date', add the afterStateUpdated callback
        if ($name === Timesheet::FRA_DATO_DATE) {
            $component->afterStateUpdated(
                fn (Set $set, ?string $state) => $set(Timesheet::TIL_DATO_DATE, Carbon::parse($state)->format('Y-m-d'))
            );
        }

        // Apply the configuration options to the component
        self::applyComponentConfig($component, $config);

        // Return the component wrapped in an array
        return [$component];
    }

    /**
     * Apply the given configuration options to a component.
     *
     * @param  object  $component  The component to apply the configuration to.
     * @param  array  $config  The configuration options to apply.
     */
    private static function applyComponentConfig(object $component, array $config): void
    {
        // Apply 'hidden' configuration option if provided
        if (isset($config['hidden'])) {
            $component->hidden($config['hidden']);
        }

        // Apply 'minDate' configuration option if provided
        if (isset($config['minDate'])) {
            $component->minDate($config['minDate']);
        }

        // Apply 'afterStateUpdated' configuration option if provided
        if (isset($config['afterStateUpdated'])) {
            $component->afterStateUpdated($config['afterStateUpdated']);
        }

        // Apply 'afterOrEqual' configuration option if provided
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
        // Create a new instance of the RichEditor class
        $richEditor = RichEditor::make($name);

        // Set the label for the rich editor
        $richEditor->label($richEditorLabel);

        // Disable specific toolbar buttons
        $richEditor->disableToolbarButtons([
            'attachFiles',
            'blockquote',
            'codeBlock',
            'h2',
            'h3',
            'link',
            'redo',
            'strike',
        ]);

        // Set the maximum length for the editor content
        $richEditor->maxLength(255);

        // Return the rich editor configuration as an array
        return [$richEditor];
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
            Hidden::make($name)->default($default),
        ];
    }
}
