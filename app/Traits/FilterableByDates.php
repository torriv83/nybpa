<?php

namespace App\Traits;

use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait FilterableByDates
{
    /**
     * Scope a query to only include records created today.
     *
     * @param  Builder<Timesheet>  $query  The query object.
     * @param  string  $column  The column name for comparison. Defaults to 'created_at'.
     * @return object The modified query object.
     */
    public function scopeToday(Builder $query, string $column = 'created_at'): object
    {
        return $query->whereDate($column, Carbon::today());
    }

    /**
     * A scope that filters the query to only include records with a specific date in the given column.
     *
     * @param  Builder<Timesheet>  $query  The query object.
     * @param  string  $column  The name of the column to filter on. Default is 'created_at'.
     * @return mixed The modified query object.
     */
    public function scopeYesterday(mixed $query, string $column = 'created_at'): mixed
    {
        return $query->whereDate($column, Carbon::yesterday());
    }

    /**
     * Scope a query to only include records from the current month to the current date.
     *
     * @param  Builder<Timesheet>  $query  The query builder instance.
     * @param  string  $column  The column to filter on. Defaults to 'created_at'.
     * @return mixed The modified query builder instance.
     */
    public function scopeMonthToDate(Builder $query, string $column = 'created_at'): mixed
    {
        return $query->whereBetween($column, [Carbon::now()->startOfMonth(), Carbon::now()]);
    }

    /**
     * Filter the query to include records from the start of the current quarter until now.
     *
     * @param  Builder<Timesheet>  $query  The query builder instance.
     * @param  string  $column  The column to filter on. Default is 'created_at'.
     * @return Builder<Timesheet> The modified query builder instance.
     */
    public function scopeQuarterToDate(Builder $query, string $column = 'created_at'): Builder
    {
        $now = Carbon::now();

        return $query->whereBetween($column, [$now->startOfQuarter(), $now]);
    }

    /**
     * A scope that filters the query to records created from the beginning of the year until now.
     *
     * @param  Builder<Timesheet>  $query  The query builder instance.
     * @param  string  $column  [optional] The column to filter on. Default is 'created_at'.
     * @return Builder<Timesheet> The modified query builder instance.
     */
    public function scopeYearToDate(Builder $query, string $column = 'created_at'): Builder
    {
        return $query->whereBetween($column, [Carbon::now()->startOfYear(), Carbon::now()]);
    }

    /**
     * @param  Builder<Timesheet>  $query
     * @return Builder<Timesheet>
     */
    public function scopeLast7Days(Builder $query, string $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::today()->subDays(6), Carbon::now()]);
    }

    /**
     * @param  Builder<Timesheet>  $query
     * @return Builder<Timesheet>
     */
    public function scopeLast30Days(Builder $query, string $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::today()->subDays(29), Carbon::now()]);
    }

    /**
     * @param  Builder<Timesheet>  $query
     * @return Builder<Timesheet>
     */
    public function scopeLastQuarter(Builder $query, string $column = 'created_at')
    {
        $now = Carbon::now();

        return $query->whereBetween($column, [$now->startOfQuarter()->subMonths(3), $now->startOfQuarter()]);
    }

    /**
     * @param  Builder<Timesheet>  $query
     * @return Builder<Timesheet>
     */
    public function scopeLastYear(Builder $query, string $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->firstOfYear()->subYear(), Carbon::now()->lastOfYear()->subYear()]);
    }

    /**
     * @param  Builder<Timesheet>  $query
     * @return Builder<Timesheet>
     */
    public function scopeLast12Months(Builder $query, string $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->firstOfYear()->subYear(), Carbon::now()->lastOfYear()->subYear()]);
    }

    /**
     * @param  Builder<Timesheet>  $query
     * @return Builder<Timesheet>
     */
    public function scopeInFuture(Builder $query, string $column = 'created_at'): mixed
    {
        return $query->where(function ($query) use ($column) {
            $currentDateTime = now();
            $currentDateOnly = $currentDateTime->format('Y-m-d');

            $query->where($column, '>', $currentDateTime)
                ->orWhere(function ($query) use ($column, $currentDateOnly, $currentDateTime) {
                    $query->whereDate($column, '=', $currentDateOnly)
                        ->where(function ($query) use ($column, $currentDateTime) {
                            $query->whereTime($column, '>', '00:00:00')
                                ->whereTime($column, '>', $currentDateTime->format('H:i:s'))
                                ->orWhereTime($column, '=', '00:00:00');
                        });
                });
        });
    }
}
