<?php

/**
 * Created by ${USER}.
 * Date: 30.09.2023
 * Time: 05.18
 * Company: Rivera Consulting
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingProgramWorkoutExercise extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'training_program_id',
        'workout_exercise_id',
        'description',
        'reps',
        'sets',
        'order',
        'rest',
    ];
}
