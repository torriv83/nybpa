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
 * @method thisYear()
 */
class Kategori extends Model
{
    use Notifiable;
    use SoftDeletes;
    
    protected $table = 'kategori';

    public $timestamps = true;
    // protected $dates      = ['fra_dato', 'til_dato'];
    protected $fillable   = [
        'kategori',
    ];

    protected static function boot()
    {

        parent::boot();
        // static::addGlobalScope('unavailable', function (Builder $builder) {

        //     $builder->where('unavailable', '=', 0);
        // });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function utstyr()
    {
        return $this->hasMany(Utstyr::class);
    }

}
