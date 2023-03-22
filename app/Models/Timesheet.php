<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * @mixin IdeHelperTimesheet
 */
class Timesheet extends Model
{

    use Notifiable;
    use SoftDeletes;

    public $timestamps = true;

    protected $casts    = [
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

    /**
     * @return BelongsTo
     */
    public function user() : BelongsTo
    {

        return $this->belongsTo(User::class, 'user_id')->withDefault(['name' => 'Tidligere ansatt']);
    }

    public function setFraDatoAttribute($value)
    {

        $this->attributes['fra_dato'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    public function setTilDatoAttribute($value)
    {

        $this->attributes['til_dato'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * @param $query
     * @method static thisYear()
     */
    public function scopethisYear($query)
    {

        $query->whereYear('til_dato', '=', date('Y'));
    }

    /**
     * @param $query
     * @method thisMonth()
     */
    public function scopethisMonth($query)
    {

        $query->whereMonth('til_dato', '=', date('m'));
    }

    /**
     * @return Collection
     */
    public function timeUsedThisYear() : Collection
    {

        // return Cache::remember('timerBruktStat', now()->addDay(), function () {
        return $this->whereBetween('fra_dato', [Carbon::parse('first day of January')->format('Y-m-d H:i:s'), Carbon::now()->endOfYear()])
                    ->where('unavailable', '!=', 1)
                    ->orderByRaw('fra_dato ASC')
                    ->get()
                    ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMM'));
    }

    /**
     * @return Collection
     */
    public function timeUsedLastYear() : Collection
    {

        // return Cache::remember('timerBruktStatForrigeAar', now()->addDay(), function () {

        return $this->whereBetween('fra_dato', [Carbon::now()->subYear()->startOfYear()->format('Y-m-d H:i:s'), Carbon::now()->subYear()->endOfYear()])
                    ->orderByRaw('fra_dato ASC')
                    ->get()
                    ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMM'));
        // });
    }

}