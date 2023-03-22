<?php

namespace App\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\UserUnavailable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\DatabaseNotification;


/**
 * @method thisYear()
 * @mixin IdeHelperKategori
 */
class Kategori extends Model
{
    use Notifiable;
    use SoftDeletes;
    
    protected $table = 'kategori';

    public $timestamps = true;

    protected $fillable   = [
        'kategori',
    ];

    protected static function boot()
    {

        parent::boot();

    }

    /**
     * @return HasMany
     */
    public function utstyr()
    {
        return $this->hasMany(Utstyr::class);
    }

}
