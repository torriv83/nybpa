<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

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

    protected $fillable = [
        'kategori',
    ];

    protected static function boot()
    {

        parent::boot();

    }

    /**
     * @return HasMany
     */
    public function utstyr(): HasMany
    {
        return $this->hasMany(Utstyr::class);
    }

}
