<?php

namespace App\Models;

use App\Traits\FilterableByDates;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * @mixin IdeHelperTimesheet
 */
class Timesheet extends Model
{
    use Notifiable;
    use SoftDeletes;
    use FilterableByDates;

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

    public function setFraDatoAttribute($value): void
    {

        $this->attributes['fra_dato'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setTilDatoAttribute($value): void
    {

        $this->attributes['til_dato'] = Carbon::parse($value)->format('Y-m-d H:i:s');
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

        return Cache::remember('timeUsedThisYear', now()->addDay(), function () {
            return $this->yearToDate()
                ->where('unavailable', '!=', 1)
                ->orderByRaw('fra_dato ASC')
                ->get()
                ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMMM'));
        });

    }

    public function timeUsedLastYear(): Collection
    {

        return Cache::remember('timeUsedLastYear', now()->addDay(), function () {

            return $this->lastYear('fra_dato')
                ->orderByRaw('fra_dato ASC')
                ->get()
                ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMMM'));
        });
    }
}
