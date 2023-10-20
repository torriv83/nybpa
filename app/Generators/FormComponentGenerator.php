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
        $component = DateTimePicker::make($name)
            ->label($label)
            ->timezone('Europe/Oslo')
            ->suffixIcon('heroicon-o-clock')
            ->native(false)
            ->disabledDates(function (Get $get, dateTimeService $dateTimeService) {
                $recordId = $get('id');

                return $dateTimeService->getAllDisabledDates($get('user_id'), $recordId) ?? [];
            })
            ->formatStateUsing(
                fn($state) => is_null($state)
                    ? Carbon::now()->minute(floor(Carbon::now()->minute / 15) * 15)->second(0)->format('Y-m-d H:i')
                    : $state
            )
            ->minDate(fn($operation) => ($operation == 'edit' || $config['isAdmin'])
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

        if ($name === Timesheet::TIL_DATO_TIME && $config['isAdmin']) {
            $component->afterStateUpdated(function (Set $set, ?string $state, Get $get, dateTimeService $dateTimeService) {
                $formattedTime = $dateTimeService->calculateFormattedTimeDifference($get(Timesheet::FRA_DATO_TIME), $state);
                $set('totalt', $formattedTime);
            });
        }

        if ($name === Timesheet::FRA_DATO_TIME) {
            $component->afterStateUpdated(function (Set $set, ?string $state, Get $get, $operation) use ($config) {
                DateTimeService::updateTilDatoTime($state, $get, $set, $operation, $config['isAdmin']);
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
            ->disabledDates(function (Get $get, DateTimeService $dateTimeService) {
                $recordId = $get('id');
                return $dateTimeService->getAllDisabledDates($get('user_id'), $recordId) ?? [];
            })
            ->suffixIcon('calendar')
            ->displayFormat('d.m.Y')
            ->minDate(fn($operation) => ($operation == 'edit' || $config['isAdmin'])
                ? null
                : Carbon::parse(today())->format('d.m.Y'))
            ->live()
            ->required();

        // If the component name is 'til_dato_date', add the afterOrEqual validation
        if ($name === Timesheet::TIL_DATO_DATE) {
            $component->afterOrEqual(Timesheet::FRA_DATO_DATE);
        }

        if ($name === Timesheet::FRA_DATO_DATE) {
            $component->afterStateUpdated(
                fn(Set $set, ?string $state) => $set(Timesheet::TIL_DATO_DATE, Carbon::parse($state)->format('Y-m-d'))
            );
        }

        self::applyComponentConfig($component, $config);

        return [$component];
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
}
