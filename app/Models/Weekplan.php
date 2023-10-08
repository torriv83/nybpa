<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weekplan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        //
    ];

    public function weekplanExercises(): HasMany
    {
        return $this->hasMany(WeekplanExercise::class);
    }

}
