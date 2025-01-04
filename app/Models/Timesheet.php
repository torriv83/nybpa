<?php

namespace App\Models;

use App\Traits\FilterableByDates;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Timesheet extends Model
{
    use Notifiable;
    use SoftDeletes;
    use FilterableByDates;
    use HasFactory;

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
        'allDay'      => '0',
    ];

    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class, 'user_id')->withDefault(['name' => 'Tidligere ansatt']);
    }

    /**
     * @method static thisYear()
     */
    public function scopethisYear($query): void
    {

        $query->whereYear('til_dato', '=', date('Y'));
    }

    /**
     * @method thisMonth()
     */
    public function scopethisMonth($query): void
    {

        $query->whereMonth('til_dato', '=', date('m'));
    }

    public function timeUsedThisYear(): Collection
    {

        return Cache::tags(['timesheet'])->remember('timeUsedThisYear', now()->addMonth(), function () {
            return $this->yearToDate()
                ->where('unavailable', '!=', 1)
                ->orderByRaw('fra_dato ASC')
                ->get()
                ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMMM'));
        });

    }

    /**
     * Retrieves the time used last year.
     *
     * @return Collection The time used last year grouped by month.
     */
    public function timeUsedLastYear(): Collection
    {

        return Cache::tags(['timesheet'])->remember('timeUsedLastYear', now()->addMonth(), function () {

            return $this->lastYear('fra_dato')
                ->where('unavailable', '=', 0)
                ->orderByRaw('fra_dato ASC')
                ->get()
                ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMMM'));
        });
    }

    /**
     * Scope a query to retrieve all disabled dates for a specific user in the current year,
     * excluding a specific record if provided.
     *
     * @param  Builder  $query
     * @param  int|null  $userId
     * @param  int|null  $recordId
     * @return Builder
     */
    public function scopeDisabledDates(Builder $query, ?int $userId, ?int $recordId): Builder
    {
        return $query->whereYear('fra_dato', Carbon::now()->year)
            ->where('user_id', '=', $userId)
            ->when($recordId, fn($query) => $query->where('id', '<>', $recordId));
    }
}
