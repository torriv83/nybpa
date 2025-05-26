<?php

/**
 * Created by Tor Rivera.
 * Date: 20.10.2023
 * Time: 02.47
 * Company: Rivera Consulting
 */

namespace App\Transformers;

use App\Constants\Timesheet;
use App\Services\DateTimeService;
use Illuminate\Support\Carbon;

class FormDataTransformer
{
    /**
     * Transforms the given form data for saving.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transformFormDataForSave(array $data): array
    {
        if ($data['allDay']) {
            $data['fra_dato'] = $data[Timesheet::FRA_DATO_DATE];
            $data['til_dato'] = $data[Timesheet::TIL_DATO_DATE];
        } else {
            $data['fra_dato'] = $data[Timesheet::FRA_DATO_TIME];
            $data['til_dato'] = $data[Timesheet::TIL_DATO_TIME];
        }

        if ($data['totalt'] ?? false) {
            $data['totalt'] = $data['unavailable'] ? 0 : DateTimeService::calculateTotalMinutes($data['totalt']);
        } else {
            $data['totalt'] = DateTimeService::calculateTotalMinutes(DateTimeService::calculateFormattedTimeDifference($data['fra_dato'], $data['til_dato']));
        }

        return $data;
    }

    /**
     * Transforms the given data array for filling.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function transformFormDataForFill(array $data): array
    {
        if ($data['allDay']) {
            $data[Timesheet::FRA_DATO_DATE] = $data['fra_dato'];
            $data[Timesheet::TIL_DATO_DATE] = $data['til_dato'];
            $data['totalt'] = 0;
        } else {
            $timezone = 'Europe/Oslo';

            $fra_dato = Carbon::parse($data['fra_dato'])->setTimezone($timezone)->format('Y-m-d H:i');
            $til_dato = Carbon::parse($data['til_dato'])->setTimezone($timezone)->format('Y-m-d H:i');

            $minutes = $data['totalt'];
            $formattedTime = sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
            $data['totalt'] = $formattedTime;

            $data[Timesheet::FRA_DATO_TIME] = $fra_dato;
            $data[Timesheet::TIL_DATO_TIME] = $til_dato;
        }

        return $data;
    }
}
