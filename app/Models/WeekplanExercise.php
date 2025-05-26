<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Class WeekplanExercise
 *
 * @property int $id
 * @property int $weekplan_id ID tilknyttet ukeplanen.
 * @property int $exercise_id ID tilknyttet en øvelse.
 * @property int $day Dag for øvelsen.
 * @property string $start_time Starttidspunkt for øvelsen.
 * @property string $end_time Sluttidspunkt for øvelsen.
 * @property string|int $intensity Intensiteten til øvelsen.
 * @property int|null $training_program_id ID til opplæringsprogrammet (valgfritt).
 * @property Carbon|null $created_at Tidspunkt for opprettelse av registrering.
 * @property Carbon|null $updated_at Tidspunkt for oppdatering av registrering.
 * @property Exercise $exercise
 * @property TrainingProgram|null $trainingProgram
 * @property Weekplan $weekplan
 *
 * under testing
 *
 * @mixin Eloquent
 */
class WeekplanExercise extends Model
{
    use Notifiable;

    protected $table = 'weekplan_exercises';

    protected $fillable = [
        'start_time',
        'end_time',
        'intensity',
        'weekplan_id',
        'exercise_id',
        'day',
        'training_program_id',
    ];

    protected $casts = [
        //
    ];

    /**
     * @phpstan-return BelongsTo<Weekplan, WeekplanExercise>
     */
    public function weekplan(): BelongsTo
    {
        return $this->belongsTo(Weekplan::class, 'weekplan_id', 'id');
    }

    /**
     * @phpstan-return BelongsTo<Exercise, WeekplanExercise>
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id', 'id');
    }

    /**
     * @phpstan-return BelongsTo<TrainingProgram, WeekplanExercise>
     */
    public function trainingProgram(): BelongsTo
    {
        return $this->belongsTo(TrainingProgram::class, 'training_program_id', 'id');
    }
}
