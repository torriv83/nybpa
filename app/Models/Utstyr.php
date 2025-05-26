<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Utstyr extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'utstyr';

    public $timestamps = true;

    // protected $dates      = ['fra_dato', 'til_dato'];
    protected $fillable = [
        'hva',
        'navn',
        'artikkelnummer',
        'antall',
        'link',
    ];

    /* @phpstan-ignore-next-line */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}
