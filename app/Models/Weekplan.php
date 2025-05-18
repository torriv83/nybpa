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

    /**
     * Retrieves a query scope that filters the query to include only active records.
     *
     * @param  mixed  $query  The query builder instance.
     * @return mixed The modified query builder instance.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
