<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

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
     * @phpstan-return \Illuminate\Database\Eloquent\Relations\HasMany<\App\Models\Utstyr, $this>
     */
    public function utstyr(): HasMany
    {
        return $this->hasMany(Utstyr::class);
    }
}
