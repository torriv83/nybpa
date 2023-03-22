<?php

namespace App\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\UserUnavailable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\DatabaseNotification;

/**
 * @mixin IdeHelperTimesheet
 */
class Timesheet extends Model
{
    use Notifiable;
    use SoftDeletes;
//TODO gå igjennom denne for å se hvilke funksjoner du ikke trenger lenger
    public $timestamps = true;
    protected $casts = [
        'resultat' => 'array',
        'fra_dato' => 'datetime',
        'til_dato' => 'datetime',
    ];
    protected $fillable   = [
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

    protected static function boot()
    {

        parent::boot();
        // static::addGlobalScope('unavailable', function (Builder $builder) {

        //     $builder->where('unavailable', '=', 0);
        // });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
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

    // public function getFraDatoAttribute($value)
    // {

    //     return Carbon::parse($value)->format('d.m.Y H:i');
    // }

    // public function getTilDatoAttribute($value)
    // {

    //     return Carbon::parse($value)->format('d.m.Y H:i');
    // }

    /**
     * @param $id
     * @param  int  $when
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public static function timerJobbet($id, $when = 0)
    {

        $ansatt = new Timesheet();

        if ($when == 0) :

            return $ansatt->thisYear()->getTimesheet($id);

        else :

            return $ansatt->getTimesheet($id);

        endif;
    }

    /**
     * @param  string  $id
     * @param  string  $orderby
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getTimesheet($id = '', $orderby = 'desc')
    {

        //->orderBy('fra_dato', $orderby);

        return $this->when($id, function ($query) use ($id) {
            return $query->where('user_id', $id);
        });
    }

    /**
     * @return int
     */
    public static function TimerIgjen()
    {

        return ((fromHoursToMinutesTotalYear()) - (new Timesheet())->TotaltTimerBrukt());
    }

    /**
     * @return mixed
     */
    public function TotaltTimerBrukt()
    {

        return Cache::remember('TotaltTimerBrukt', now()->addDay(), function () {

            return $this->thisYear()->sum('totalt');
        });
    }

    /**
     * @param  string  $id
     *
     * @return mixed
     */
    public static function OffPageSidebar($id = '')
    {

        if ($id != '') {
            return Cache::remember('OffPageSidebar' . $id, now()->addDay(), function () use ($id) {

                return (new Timesheet())->thisYear()->with([
                    'user' => function ($q) {

                        $q->withTrashed();
                    }
                ])->withTrashed()->when($id, function ($query) use ($id) {

                    return $query->where('user_id', $id);
                })->latest('fra_dato')->get();
            });
        } else {

            return Cache::remember('OffPageSidebar', now()->addDay(), function () {

                return (new Timesheet())->thisYear()->with([
                    'user' => function ($q) {

                        $q->withTrashed();
                    }
                ])->withTrashed()->latest('fra_dato')->limit(12)->get();
            });
        }
    }

    /**
     * @return string
     */
    public function jobbDato()
    {

        return Carbon::parse($this->fra_dato)->Format('d.m.Y');
    }

    /**
     * @return string
     */
    public function jobbDatoTil()
    {

        return Carbon::parse($this->til_dato)->Format('d.m.Y');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function jobbTotaltDag()
    {

        $dt = new Carbon();

        $start     = $dt->parse($this->jobbFra());
        $slutt     = $dt->parse($this->jobbTil());
        $utenTimer = $start->diffInHours($slutt);

        return $start->diffInHours($slutt) . ':' . $start->addHours($utenTimer)->diff($slutt)->format('%I');
    }

    /**
     * @return string
     */
    public function jobbFra()
    {

        return Carbon::parse($this->fra_dato)->Format('H:i');
    }

    /**
     * @return string
     */
    public function jobbTil()
    {

        return Carbon::parse($this->til_dato)->Format('H:i');
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
     * @param $query
     * @method thisWeek()
     */
    public function scopethisWeek($query)
    {

        $query->WhereBetween('fra_dato', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
    }

    /**
     * @param $query
     * @method static TimelisteDenneMaaneden()
     */
    public function scopeTimelisteDenneMaaneden($query)
    {

        $query->whereMonth('fra_dato', '=', Carbon::now()->format('m'))->whereYear('fra_dato', '=', Carbon::now()->format('Y'));
    }

    /**
     * @param $query
     * @method static TimelisteForrigeMaaned()
     *
     * @throws \Exception
     */
    public function scopeTimelisteForrigeMaaned($query)
    {

        $month = Carbon::now()->subMonth()->format('m');
        if ((new Carbon(now()))->format('m') == '01') {
            $year  = Carbon::now()->subYear()->format('Y');
        } else {
            $year  = Carbon::now()->format('Y');
        }

        $query->whereMonth('fra_dato', '=', $month)->whereYear('fra_dato', '=', $year);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function timeUsedThisYear()
    {

        // return Cache::remember('timerBruktStat', now()->addDay(), function () {
        return $this->whereBetween('fra_dato', [Carbon::parse('first day of January')->format('Y-m-d H:i:s'), Carbon::now()->endOfYear()])
            ->where('unavailable', '!=', 1)
            ->orderByRaw('fra_dato ASC')
            ->get()
            ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMM'));
    }

    public function timeUsedLastYear()
    {

        // return Cache::remember('timerBruktStatForrigeAar', now()->addDay(), function () {

        return $this->whereBetween('fra_dato', [Carbon::now()->subYear()->startOfYear()->format('Y-m-d H:i:s'), Carbon::now()->subYear()->endOfYear()])
            ->orderByRaw('fra_dato ASC')
            ->get()
            ->groupBy(fn($val) => Carbon::parse($val->fra_dato)->isoFormat('MMM'));
        // });
    }

    /**
     * @param $start
     * @param $end
     * @param  string  $id
     *
     * @return bool
     */
    public function checkTime($start, $end, $id = '', $unavailable = '', $userID = '')
    {

        //Sjekker om det allerede er registrert tid i samme tidsrom
        if ($unavailable == 0) {
            if ($id != '') {
                return $this->withoutGlobalScope('unavailable')->where('fra_dato', '<=', Carbon::parse($end)->format('Y-m-d H:i:s'))
                    ->where('til_dato', '>=', Carbon::parse($start)->format('Y-m-d H:i:s'))
                    ->where('id', '!=', $id)
                    ->where('unavailable', '=', $unavailable)->exists();
            } else {
                return $this->where('fra_dato', '<=', Carbon::parse($end)->format('Y-m-d H:i:s'))
                    ->where('til_dato', '>=', Carbon::parse($start)->format('Y-m-d H:i:s'))->exists();
            }
        } else {
            if ($id != '') {
                return $this->withoutGlobalScope('unavailable')->where('fra_dato', '<=', Carbon::parse($end)->format('Y-m-d H:i:s'))
                    ->where('til_dato', '>=', Carbon::parse($start)->format('Y-m-d H:i:s'))
                    ->where('id', '!=', $id)
                    ->where('user_id', '=', $userID)
                    ->where('unavailable', '=', $unavailable)->exists();
            } else {
                return $this->where('fra_dato', '<=', Carbon::parse($end)->format('Y-m-d H:i:s'))
                    ->where('til_dato', '>=', Carbon::parse($start)->format('Y-m-d H:i:s'))->exists();
            }
        }
    }
}
