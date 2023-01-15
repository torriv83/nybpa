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
class Utstyr extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'utstyr';

    public $timestamps = true;
    // protected $dates      = ['fra_dato', 'til_dato'];
    protected $fillable   = [
        'hva',
        'navn',
        'artikkelnummer',
        'antall',
        'link',
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
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategoriID');
    }


}
