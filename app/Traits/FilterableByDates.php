<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait FilterableByDates
{
    /**
     * Scope a query to only include records created today.
     *
     * @param  object  $query  The query object.
     * @param  string  $column  The column name for comparison. Defaults to 'created_at'.
     * @return object The modified query object.
     */
    public function scopeToday(object $query, string $column = 'created_at'): object
    {
        return $query->whereDate($column, Carbon::today());
    }

    /**
     * A scope that filters the query to only include records with a specific date in the given column.
     *
     * @param  mixed  $query  The query object.
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
     * @param  mixed  $query  The query builder instance.
     * @param  string  $column  The column to filter on. Defaults to 'created_at'.
     * @return mixed The modified query builder instance.
     */
    public function scopeMonthToDate(mixed $query, string $column = 'created_at'): mixed
    {
        return $query->whereBetween($column, [Carbon::now()->startOfMonth(), Carbon::now()]);
    }

    /**
     * Filter the query to include records from the start of the current quarter until now.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  string  $column  The column to filter on. Default is 'created_at'.
     * @return Builder The modified query builder instance.
     */
    public function scopeQuarterToDate(Builder $query, string $column = 'created_at'): Builder
    {
        $now = Carbon::now();

        return $query->whereBetween($column, [$now->startOfQuarter(), $now]);
    }

    /**
     * A scope that filters the query to records created from the beginning of the year until now.
     *
     * @param  Builder  $query  The query builder instance.
     * @param  string  $column  [optional] The column to filter on. Default is 'created_at'.
     * @return Builder The modified query builder instance.
     */
    public function scopeYearToDate(Builder $query, string $column = 'created_at'): Builder
    {
        return $query->whereBetween($column, [Carbon::now()->startOfYear(), Carbon::now()]);
    }

    public function scopeLast7Days($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::today()->subDays(6), Carbon::now()]);
    }

    public function scopeLast30Days($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::today()->subDays(29), Carbon::now()]);
    }

    public function scopeLastQuarter($query, $column = 'created_at')
    {
        $now = Carbon::now();

        return $query->whereBetween($column, [$now->startOfQuarter()->subMonths(3), $now->startOfQuarter()]);
    }

    public function scopeLastYear($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->firstOfYear()->subYear(), Carbon::now()->lastOfYear()->subYear()]);
    }

    public function scopeLast12Months($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [Carbon::now()->firstOfYear()->subYear(), Carbon::now()->lastOfYear()->subYear()]);
    }

    public function scopeInFuture($query, string $column = 'created_at'): mixed
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
