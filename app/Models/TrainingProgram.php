<?php

/**
 * Created by ${USER}.
 * Date: 30.09.2023
 * Time: 04.52
 * Company: Rivera Consulting
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProgram extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'program_name',
        'description',
    ];

    public function WorkoutExercises(): BelongsToMany
    {
        return $this->belongsToMany(WorkoutExercise::class)->withPivot('repetitions', 'sets', 'order', 'rest', 'description');
    }
}
