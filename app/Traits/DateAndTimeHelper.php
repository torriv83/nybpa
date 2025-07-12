<?php

namespace App\Traits;

use App\Constants\Timesheet;
use App\Generators\FormComponentGenerator;

trait DateAndTimeHelper
{
    /**
     * Retrieves the common fields for a specific user role.
     *
     * @param  bool  $isAdmin  Indicates whether the user is an admin or not.
     * @return array<int, \Filament\Forms\Components\Component> An array containing the common fields.
     */
    public static function getCommonFields(bool $isAdmin): array
    {
        // Initialize an empty array to store the common fields
        $fields = [];

        // Configuration for the date time picker
        $dateTimePickerConfig = [
            'hidden' => fn ($get) => $get('allDay'), // Callback function to determine if the picker is hidden
            'isAdmin' => $isAdmin, // Indicates whether the user is an admin or not
        ];

        // Configuration for the date picker
        $datePickerConfig = [
            'hidden' => fn ($get) => ! $get('allDay'), // Callback function to determine if the picker is hidden
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
            FormComponentGenerator::dynamicDateTimePicker(Timesheet::FRA_DATO_TIME, 'Fra dato', $dateTimePickerConfig),
            FormComponentGenerator::dynamicDateTimePicker(Timesheet::TIL_DATO_TIME, 'Til dato', $dateTimePickerConfig),
            FormComponentGenerator::dynamicDatePicker(Timesheet::FRA_DATO_DATE, 'Fra dato', $datePickerConfig),
            FormComponentGenerator::dynamicDatePicker(Timesheet::TIL_DATO_DATE, 'Til dato', $datePickerConfig),
            FormComponentGenerator::dynamicRichEditor('description', $richEditorLabel),
            FormComponentGenerator::dynamicHidden('totalt', 0),
            FormComponentGenerator::dynamicHidden('unavailable', true)
        );
    }
}
