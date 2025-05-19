<?php

/**
 * Created by ${USER}.
 * Date: 30.09.2023
 * Time: 05.06
 * Company: Rivera Consulting
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkoutExercise extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'exercise_name',
    ];

    public function TrainingPrograms(): BelongsToMany
    {
        return $this->belongsToMany(TrainingProgram::class)->withPivot('repetitions', 'sets', 'order', 'rest', 'description');
    }
}
