<?php

namespace App\Models;

use App\Traits\FilterableByDates;
use Carbon\Carbon;
use Database\Factories\TimesheetFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Timesheet extends Model
{
    use FilterableByDates;

    /** @use HasFactory<TimesheetFactory> */
    use HasFactory;

    use Notifiable;
    use SoftDeletes;

    public $timestamps = true;

    protected $casts = [
        'resultat' => 'array',
        'fra_dato' => 'datetime',
        'til_dato' => 'datetime',
    ];

    protected $fillable = [
        'fra_dato',
        'til_dato',
        'description',
        'totalt',
        'user_id',
        'unavailable',
        'allDay',
    ];

    protected $attributes = [
        'unavailable' => '0',
        'allDay' => '0',
    ];

    /**
     * Get the user associated with this timesheet.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\App\Models\User, $this>
     */
    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class, 'user_id')->withDefault(['name' => 'Tidligere ansatt']);
    }

    /**
     * Scope a query to only include records from the current year.
     *
     * @param  Builder<\App\Models\Timesheet>  $query
     * @return Builder<\App\Models\Timesheet>
     */
    public function scopethisYear(Builder $query): Builder
    {
        return $query->whereYear('til_dato', '=', date('Y'));
    }

    /**
     * @method thisMonth()
     *
     * @param  Builder<\App\Models\Timesheet>  $query
     * @return Builder<\App\Models\Timesheet>
     */
    public function scopethisMonth(Builder $query): Builder
    {

        return $query->whereMonth('til_dato', '=', date('m'));
    }

    /**
     * @phpstan-return Collection<string, EloquentCollection<int, Timesheet>>
     *   The time used this year grouped by month.
     */
    public function timeUsedThisYear(): Collection
    {

        return Cache::tags(['timesheet'])->remember('timeUsedThisYear', now()->addMonth(), function () {
            return $this->yearToDate()
                ->where('unavailable', '!=', 1)
                ->orderByRaw('fra_dato ASC')
                ->get()
                ->groupBy(fn ($val) => Carbon::parse($val->fra_dato)->isoFormat('MMMM'));
        });

    }

    /**
     * Retrieves the time used last year.
     *
     * @return Collection The time used last year grouped by month.
     *
     * @phpstan-ignore-next-line
     */
    public function timeUsedLastYear(): Collection
    {

        return Cache::tags(['timesheet'])->remember('timeUsedLastYear', now()->addMonth(), function () {

            return $this->lastYear('fra_dato')
                ->where('unavailable', '=', 0)
                ->orderByRaw('fra_dato ASC')
                ->get()
                ->groupBy(fn ($val) => Carbon::parse($val->fra_dato)->isoFormat('MMMM'));
        });
    }

    /**
     * Scope a query to retrieve all disabled dates for a specific user in the current year,
     * excluding a specific record if provided.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<\App\Models\Timesheet>  $query
     *
     * @phpstan-param \Illuminate\Database\Eloquent\Builder<\App\Models\Timesheet> $query
     *
     * @phpstan-return \Illuminate\Database\Eloquent\Builder<\App\Models\Timesheet>
     */
    public function scopeDisabledDates(Builder $query, ?int $userId, ?int $recordId): Builder
    {
        return $query->whereYear('fra_dato', Carbon::now()->year)
            ->where('user_id', '=', $userId)
            ->when($recordId, fn ($query) => $query->where('id', '<>', $recordId));
    }
}
