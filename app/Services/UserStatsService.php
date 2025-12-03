<?php

/**
 * Created by Tor Rivera.
 * Date: 03.06.2023
 * Time: 09.49
 * Company: Rivera Consulting
 */

namespace App\Services;

use App\Models\Settings;
use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UserStatsService
{
    public const DAYS_IN_WEEK = 7;

    public const WEEKS_IN_YEAR = 52;

    public const MINUTES_IN_HOUR = 60;

    public const HOURS_IN_DAY = 24;

    public const FRA_DATO = 'fra_dato';

    public const TIL_DATO = 'til_dato';

    protected Timesheet $timesheet;

    protected mixed $bpa;

    public function __construct()
    {
        $this->timesheet = new Timesheet;

        $this->bpa = Settings::getUserBpa();
    }

    /**
     * Get the number of assistants.
     */
    public function getNumberOfAssistents(): int
    {
        return Cache::tags(['timesheet'])->remember('number-of-assisstents', now()->addWeek(), function () {
            return User::assistenter()->count();
        });
    }

    /**
     * Get the hours used this year.
     */
    public function getHoursUsedThisYear(): string
    {
        return Cache::tags(['timesheet'])->remember('hours-used-this-year', now()->addWeek(), function () {
            $tider = $this->timesheet->yearToDate(self::FRA_DATO)->where('unavailable', '!=', '1');
            $hoursUsed = $tider->sum('totalt');

            return $this->minutesToTime($hoursUsed);
        });
    }

    /**
     * Get the yearly time chart.
     *
     * @return array<string, float>
     */
    public function getYearlyTimeChart(): array
    {
        $thisYear = $this->timesheet->timeUsedThisYear();
        $thisYearTimes = [];
        $totalMinutes = 0;

        // Avoid division by zero and clarify rounding precision
        $roundPrecision = 1;
        $bpa = (float) ($this->bpa ?? 0.0);

        foreach ($thisYear as $key => $value) {
            $number = count($value);
            for ($i = 0; $i < $number; $i++) {
                $totalMinutes += $value[$i]->totalt;
            }

            $percentageOfBpaUsed = $bpa > 0.0
                ? round(($totalMinutes / $bpa) * 100, $roundPrecision)
                : 0.0;

            $thisYearTimes[$key] = $percentageOfBpaUsed;
        }

        return $thisYearTimes;
    }

    /**
     * Get the yearly time filters.
     *
     * @return array<string, array<string, array<string, string>>>
     */
    public function getYearlyTimeFilters(): array
    {
        $currentYear = Carbon::now()->year;
        $firstDayOfYear = Carbon::createFromDate($currentYear, 1, 1)->format('Y-m-d');
        $lastDayOfYear = Carbon::createFromDate($currentYear, 12, 31)->format('Y-m-d');

        return [
            'tableFilters' => [
                'måned' => [
                    self::FRA_DATO => $firstDayOfYear,
                    self::TIL_DATO => $lastDayOfYear,
                ],
            ],
        ];
    }

    /**
     * Get the description of hours used this month.
     */
    public function getHoursUsedThisMonthDescription(): string
    {
        return Cache::tags(['timesheet'])->remember('hours-used-this-month-description', now()->addWeek(), function () {
            $hoursToUseThisMonth = ($this->bpa / self::DAYS_IN_WEEK) * Carbon::now()->daysInMonth;
            $usedThisMonth = $this->timesheet->monthToDate(self::FRA_DATO)->where('unavailable', '=', '0')->sum('totalt');

            return $this->minutesToTime($usedThisMonth).' brukt av '.$hoursToUseThisMonth.' denne måneden.';
        });
    }

    /**
     * Get the remaining hours.
     */
    public function getRemainingHours(): string
    {
        $totalMinutes = ($this->bpa * self::WEEKS_IN_YEAR) * self::MINUTES_IN_HOUR;
        $hoursUsedMinutes = $this->getHoursUsedInMinutes();
        $remainingMinutes = $totalMinutes - $hoursUsedMinutes;

        return $this->minutesToTime($remainingMinutes);
    }

    /**
     * Get the average hours per week description.
     */
    public function getAverageHoursPerWeekDescription(): string
    {
        $totalMinutesForYear = ($this->bpa * self::WEEKS_IN_YEAR) * self::MINUTES_IN_HOUR;
        $hoursUsedMinutes = $this->getHoursUsedInMinutes();
        $remainingMinutes = $totalMinutesForYear - $hoursUsedMinutes;

        // Antall dager igjen i året
        $daysRemainingInYear = Carbon::now()->diffInDays(Carbon::now()->endOfYear()) + 1;

        // Beregn resterende uker (minimum 1 uke hvis på slutten av året)
        $weeksRemaining = max(1, $daysRemainingInYear / self::DAYS_IN_WEEK);

        // Beregn gjennomsnittlige timer per uke
        $leftPerWeek = ($remainingMinutes / self::MINUTES_IN_HOUR) / $weeksRemaining;

        return $this->calculateAvgPerWeek($leftPerWeek);
    }

    /**
     * Convert minutes to time format.
     */
    public function minutesToTime(int $minutes): string
    {
        $hours = intdiv($minutes, self::MINUTES_IN_HOUR);
        $minutes = ($minutes % self::MINUTES_IN_HOUR);
        $format = '%02d:%02d';

        return sprintf($format, $hours, $minutes);
    }

    /**
     * Calculate average per week.
     */
    private function calculateAvgPerWeek(float $leftPerWeek): string
    {
        $hours = (int) floor($leftPerWeek);
        $minutes = (int) round(($leftPerWeek - $hours) * self::MINUTES_IN_HOUR);

        return sprintf('%02d:%02d', $hours, $minutes);
    }

    /**
     * Get hours used in minutes
     *
     * @return int|mixed
     */
    private function getHoursUsedInMinutes(): mixed
    {
        return Cache::tags(['timesheet'])->remember('hoursUsedInMinutes', now()->addWeek(), function () {
            return $this->timesheet->yearToDate('fra_dato')->where('unavailable', '!=', '1')->sum('totalt');
        });
    }

    /**
     * Get the total hours used this week (only past entries, not planned).
     *
     * @return string
     */
    public function getHoursUsedThisWeek()
    {
        return Cache::tags(['timesheet'])->remember('getHoursUsedThisWeek', now()->addWeek(), function () {
            $startOfWeek = now()->startOfWeek();
            $now = now();

            // Only get entries from start of week until now (exclude future/planned)
            $minutes = Timesheet::query()
                ->where('timesheets.unavailable', '!=', 1)
                ->where('timesheets.'.self::FRA_DATO, '>=', $startOfWeek)
                ->where('timesheets.'.self::FRA_DATO, '<=', $now)
                ->sum('timesheets.totalt');

            return $this::minutesToTime($minutes);
        });
    }

    /**
     * Get planned hours for the rest of the year.
     */
    public function getPlannedHoursRestOfYear(): string
    {
        return Cache::tags(['timesheet'])->remember('planned-hours-rest-of-year', now()->addWeek(), function () {
            $plannedMinutes = Timesheet::query()
                ->inFuture(self::FRA_DATO)
                ->whereYear(self::FRA_DATO, Carbon::now()->year)
                ->where('unavailable', '!=', 1)
                ->sum('totalt');

            return $this->minutesToTime($plannedMinutes);
        });
    }

    /**
     * Get remaining hours after subtracting both used and planned hours.
     */
    public function getRemainingHoursWithPlanned(): string
    {
        return Cache::tags(['timesheet'])->remember('planned-hours-remaining', now()->addWeek(), function () {
            $totalMinutesForYear = ($this->bpa * self::WEEKS_IN_YEAR) * self::MINUTES_IN_HOUR;
            $hoursUsedMinutes = $this->getHoursUsedInMinutes();

            $plannedMinutes = Timesheet::query()
                ->inFuture(self::FRA_DATO)
                ->whereYear(self::FRA_DATO, Carbon::now()->year)
                ->where('unavailable', '!=', 1)
                ->sum('totalt');

            $remainingAfterPlanned = $totalMinutesForYear - $hoursUsedMinutes - $plannedMinutes;

            return $this->minutesToTime(max(0, (int) $remainingAfterPlanned));
        });
    }

    /**
     * Get planned hours for the rest of the current week.
     */
    public function getPlannedHoursThisWeek(): string
    {
        return Cache::tags(['timesheet'])->remember('planned-hours-this-week', now()->addWeek(), function () {
            $endOfWeek = now()->endOfWeek();

            $plannedMinutes = Timesheet::query()
                ->inFuture(self::FRA_DATO)
                ->where(self::FRA_DATO, '<=', $endOfWeek)
                ->where('unavailable', '!=', 1)
                ->sum('totalt');

            return $this->minutesToTime($plannedMinutes);
        });
    }
}
