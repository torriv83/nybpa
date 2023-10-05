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
    protected Timesheet $timesheet;

    protected mixed $bpa;

    public function __construct()
    {
        $this->timesheet = new Timesheet();

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
            $tider     = $this->timesheet->yearToDate('fra_dato')->where('unavailable', '!=', '1');
            $hoursUsed = $tider->sum('totalt');

            return $this->minutesToTime($hoursUsed);
        });
    }

    /**
     * Get the yearly time chart.
     */
    public function getYearlyTimeChart(): array
    {
        $thisYear      = $this->timesheet->TimeUsedThisYear();
        $thisYearTimes = [];
        $sum           = 0;

        foreach ($thisYear as $key => $value) {
            $number = count($value);
            for ($i = 0; $i < $number; $i++) {
                $sum += $value[$i]->totalt;
            }
            $thisYearTimes[$key] = round($sum / $this->bpa * 100, 1);
        }

        return $thisYearTimes;
    }

    /**
     * Get the yearly time filters.
     */
    public function getYearlyTimeFilters(): array
    {
        $currentYear    = Carbon::now()->year;
        $firstDayOfYear = Carbon::createFromDate($currentYear, 1, 1)->format('Y-m-d');
        $lastDayOfYear  = Carbon::createFromDate($currentYear, 12, 31)->format('Y-m-d');

        return [
            'tableFilters' => [
                'måned' => [
                    'fra_dato' => $firstDayOfYear,
                    'til_dato' => $lastDayOfYear,
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
            $hoursToUseThisMonth = ($this->bpa / 7) * Carbon::now()->daysInMonth;
            $usedThisMonth = $this->timesheet->monthToDate('fra_dato')->where('unavailable', '=', '0')->sum('totalt');

            return $this->minutesToTime($usedThisMonth) . ' brukt av ' . $hoursToUseThisMonth . ' denne måneden.';
        });
    }


    /**
     * Get the remaining hours.
     */
    public function getRemainingHours(): string
    {
        $totalMinutes     = ($this->bpa * 52) * 60;
        $hoursUsedMinutes = $this->getHoursUsedInMinutes();
        $remainingMinutes = $totalMinutes - $hoursUsedMinutes;

        return $this->minutesToTime($remainingMinutes);
    }

    /**
     * Get the average hours per week description.
     */
    public function getAverageHoursPerWeekDescription(): string
    {
        $hoursUsedMinutes = $this->getHoursUsedInMinutes();
        $weeksLeft        = Carbon::now()->floatDiffInWeeks(Carbon::now()->endOfYear());
        $totalMinutes     = ($this->bpa * 52) * 60;
        $hoursPerWeek     = 24 * 7;
        $leftPerWeek      = (($totalMinutes - $hoursUsedMinutes) / 60 - ($hoursPerWeek * $weeksLeft)) / $weeksLeft;

        return $this->calculateAvgPerWeek($leftPerWeek);
    }

    /**
     * Convert minutes to time format.
     */
    public function minutesToTime(int $minutes): string
    {
        $hours   = $minutes / 60;
        $minutes = ($minutes % 60);
        $format  = '%02d:%02d';

        return sprintf($format, $hours, $minutes);
    }

    /**
     * Calculate average per week.
     */
    private function calculateAvgPerWeek(float $leftPerWeek): string
    {
        $hours   = floor($leftPerWeek);
        $minutes = floor(($leftPerWeek - $hours) * 60);
        $seconds = round((($leftPerWeek - $hours) * 60 - $minutes) * 60);

        return date('H:i:s', mktime($hours, $minutes, $seconds, 0, 0, 0));
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

    public function getHoursUsedThisWeek()
    {
        return Cache::tags(['timesheet'])->remember('getHoursUsedThisWeek', now()->addWeek(), function(){
            // Get the start and end of the current week
            $startOfWeek = now()->startOfWeek();
            $endOfWeek = now()->endOfWeek();

            // Run the query
            $minutes = Timesheet::query()
                ->where('timesheets.unavailable', '!=', 1)
                ->whereBetween('timesheets.fra_dato', [$startOfWeek, $endOfWeek])
                ->sum('timesheets.totalt');

            return self::minutesToTime($minutes);
        });
    }
}
