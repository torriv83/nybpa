<?php

/**
 * Created by Tor Rivera.
 * Date: 20.10.2023
 * Time: 02.23
 * Company: Rivera Consulting
 */

namespace App\Services;

use App\Constants\Timesheet as TimesheetConstants;
use App\Models\Timesheet;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DateTimeService
{
    /**
     * Calculates the total number of minutes from a given time string.
     *
     * @param  string  $time  The time string in the format "hh:mm".
     * @return int The total number of minutes calculated from the time string.
     */
    public static function calculateTotalMinutes(string $time): int
    {
        if (preg_match('/(\d+):(\d+)/', $time, $matches)) {
            $hours = intval($matches[1]);
            $minutes = intval($matches[2]);

            return $hours * 60 + $minutes;
        }

        return 0;
    }

    /**
     * Calculates the formatted time difference between two given timestamps.
     *
     * @param  string  $startTime  The starting timestamp.
     * @param  string  $endTime  The ending timestamp.
     * @return string The formatted time difference in the format hh:mm.
     */
    public static function calculateFormattedTimeDifference(string $startTime, string $endTime): string
    {
        $fromDate = Carbon::parse($startTime);
        $toDate = Carbon::parse($endTime);
        $minutes = $fromDate->diffInMinutes($toDate);

        // Format minutes to hh:mm
        return sprintf('%02d:%02d', intdiv((int) $minutes, 60), (int) $minutes % 60);
    }

    /**
     * Retrieves an array of all disabled dates for a specific user excluding current record.
     *
     * @param  int|null  $userId  The ID of the user.
     * @param  int|null  $recordId  The ID of the record.
     * @return array<int, string> An array of disabled dates (formatted as Y-m-d).
     */
    public static function getAllDisabledDates(?int $userId, ?int $recordId): array
    {
        $cacheKey = "disabled_dates:user_$userId:record_$recordId";

        return Cache::tags(['timesheet'])->remember($cacheKey, now()->addMonth(), function () use ($userId, $recordId) {
            return Timesheet::disabledDates($userId, $recordId)
                ->pluck('fra_dato')
                ->unique()
                ->map(fn ($date) => $date->format('Y-m-d'))
                ->toArray();
        });
    }

    /**
     * Updates the 'til_dato_time' based on the given state, Get and Set objects, operation, and isAdmin flag.
     *
     * @param  string|null  $state  The new state to parse.
     * @param  Get  $get  The Get object to retrieve values.
     * @param  Set  $set  The Set object to update values.
     * @param  mixed  $operation  The operation to perform.
     * @param  bool  $isAdmin  Flag indicating if the user is an admin.
     */
    public static function updateTilDatoTime(?string $state, Get $get, Set $set, mixed $operation, bool $isAdmin): void
    {
        // Parse the new state and the previous 'til_dato_time'
        $newFraDato = Carbon::parse($state);
        $existingTilDato = Carbon::parse($get(TimesheetConstants::TIL_DATO_TIME));

        // Check if only the date part has changed
        if ($newFraDato->format('Y-m-d') !== $existingTilDato->format('Y-m-d')) {
            // Only the date part has changed, so update the date part of 'til_dato_time' without changing the time part
            $updatedTilDato = $existingTilDato->setDate($newFraDato->year, $newFraDato->month, $newFraDato->day);
            $set(TimesheetConstants::TIL_DATO_TIME, $updatedTilDato->format('Y-m-d H:i'));
        } elseif ($operation == 'create') {
            $set(TimesheetConstants::TIL_DATO_TIME, $newFraDato->addHour()->format('Y-m-d H:i'));
        } else {
            $totalt = $get('totalt');
            [$hours, $minutes] = explode(':', $totalt);
            $durationInMinutes = ((int) $hours * 60) + (int) $minutes;

            // Set the updated 'til_dato_time' by adding the duration to the new 'fra_dato_time'
            $updatedTilDato = $newFraDato->copy()->addMinutes($durationInMinutes);
            $set(TimesheetConstants::TIL_DATO_TIME, $updatedTilDato->format('Y-m-d H:i'));
        }

        // If isAdmin is true, also update 'totalt'
        if ($isAdmin) {
            $formattedTime = (new DateTimeService)->calculateFormattedTimeDifference($state, $get(TimesheetConstants::TIL_DATO_TIME));
            $set('totalt', $formattedTime);
        }
    }
}
