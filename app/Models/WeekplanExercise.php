<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class WeekplanExercise extends Model
{
    use HasFactory;
    use Notifiable;

    protected $table = 'weekplan_exercises';

    protected $fillable = [
        'start_time',
        'end_time',
        'intensity',
        'weekplan_id',
        'exercise_id',
        'day',
    ];

    protected $casts = [
        //
    ];

    public function weekplan(): BelongsTo
    {
        return $this->belongsTo(Weekplan::class);
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }

}
