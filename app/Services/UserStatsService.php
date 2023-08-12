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
use App\Models\Utstyr;
use Carbon\Carbon;

class UserStatsService
{
    protected Timesheet $timesheet;

    protected mixed $bpa;

    public function __construct()
    {
        $this->timesheet = app(Timesheet::class);
        $setting         = Settings::where('user_id', '=', \Auth::id())->first();
        $this->bpa       = $setting['bpa_hours_per_week'] ?? 1;
    }

    /**
     * Get the number of assistants.
     */
    public function getNumberOfAssistents(): int
    {
        //TODO aktiver når phpsa auth er kompatibel med filamen v3
        //return User::assistenter()->count();
        return User::count();
    }

    /**
     * Get the hours used this year.
     */
    public function getHoursUsedThisYear(): string
    {
        $tider     = $this->timesheet->yearToDate('fra_dato')->where('unavailable', '!=', '1');
        $hoursUsed = $tider->sum('totalt');

        return $this->minutesToTime($hoursUsed);
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
        $hoursToUseThisMonth = ($this->bpa / 7) * Carbon::now()->daysInMonth;
        $usedThisMonth       = $this->timesheet->monthToDate('fra_dato')->where('unavailable', '=', '0')->sum('totalt');

        return $this->minutesToTime($usedThisMonth) . ' brukt av ' . $hoursToUseThisMonth . ' denne måneden.';
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
     * Get the number of equipment.
     */
    public function getNumberOfEquipment(): int
    {
        return Utstyr::all()->count();
    }

    /**
     * Convert minutes to time format.
     */
    private function minutesToTime(int $minutes): string
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
        return \Cache::remember('hoursUsedInMinutes', now()->addDay(), function () {
            return $this->timesheet->yearToDate('fra_dato')->where('unavailable', '!=', '1')->sum('totalt');
        });

    }
}
