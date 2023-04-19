<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weekplan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'data'
    ];

    protected $casts = ['data' => 'json'];

    public function days(): BelongsToMany
    {
        return $this->belongsToMany(Day::class, 'days');
    }
}
