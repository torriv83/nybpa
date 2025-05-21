<?php

namespace Tests\Unit\Models;

use App\Models\Exercise;
use App\Models\TrainingProgram;
use App\Models\Weekplan;
use App\Models\WeekplanExercise;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WeekplanExerciseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_weekplan_exercise()
    {
        $weekplan = Weekplan::create(['name' => 'Test Weekplan']);
        $exercise = Exercise::create(['name' => 'Test Exercise']);

        $weekplanExercise = WeekplanExercise::create([
            'weekplan_id' => $weekplan->id,
            'exercise_id' => $exercise->id,
            'day' => 1,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'intensity' => 'green',
        ]);

        $this->assertInstanceOf(WeekplanExercise::class, $weekplanExercise);
        $this->assertEquals(1, $weekplanExercise->day);
        $this->assertEquals('09:00', $weekplanExercise->start_time);
        $this->assertEquals('10:00', $weekplanExercise->end_time);
        $this->assertEquals('green', $weekplanExercise->intensity);
    }

    #[Test]
    public function it_belongs_to_a_weekplan()
    {
        $weekplan = Weekplan::create(['name' => 'Test Weekplan']);
        $exercise = Exercise::create(['name' => 'Test Exercise']);

        $weekplanExercise = WeekplanExercise::create([
            'weekplan_id' => $weekplan->id,
            'exercise_id' => $exercise->id,
            'day' => 1,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'intensity' => 'green',
        ]);

        $this->assertInstanceOf(Weekplan::class, $weekplanExercise->weekplan);
        $this->assertEquals($weekplan->id, $weekplanExercise->weekplan->id);
        $this->assertEquals('Test Weekplan', $weekplanExercise->weekplan->name);
    }

    #[Test]
    public function it_belongs_to_an_exercise()
    {
        // Create an exercise first
        $exercise = Exercise::create(['name' => 'Test Exercise']);

        $weekplan = Weekplan::create(['name' => 'Test Weekplan']);

        $weekplanExercise = WeekplanExercise::create([
            'weekplan_id' => $weekplan->id,
            'exercise_id' => $exercise->id,
            'day' => 1,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'intensity' => 'green',
        ]);

        $this->assertInstanceOf(Exercise::class, $weekplanExercise->exercise);
        $this->assertEquals($exercise->id, $weekplanExercise->exercise->id);
        $this->assertEquals('Test Exercise', $weekplanExercise->exercise->name);
    }

    #[Test]
    public function it_can_have_a_training_program()
    {
        // Create a training program first
        $trainingProgram = TrainingProgram::create(['program_name' => 'Test Program']);

        $weekplan = Weekplan::create(['name' => 'Test Weekplan']);
        $exercise = Exercise::create(['name' => 'Test Exercise']);

        $weekplanExercise = WeekplanExercise::create([
            'weekplan_id' => $weekplan->id,
            'exercise_id' => $exercise->id,
            'day' => 1,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'intensity' => 'green',
            'training_program_id' => $trainingProgram->id,
        ]);

        $this->assertInstanceOf(TrainingProgram::class, $weekplanExercise->trainingProgram);
        $this->assertEquals($trainingProgram->id, $weekplanExercise->trainingProgram->id);
        $this->assertEquals('Test Program', $weekplanExercise->trainingProgram->program_name);
    }

    #[Test]
    public function it_can_update_a_weekplan_exercise()
    {
        $weekplan = Weekplan::create(['name' => 'Test Weekplan']);
        $exercise = Exercise::create(['name' => 'Test Exercise']);

        $weekplanExercise = WeekplanExercise::create([
            'weekplan_id' => $weekplan->id,
            'exercise_id' => $exercise->id,
            'day' => 1,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'intensity' => 'green',
        ]);

        // Update the weekplan exercise
        $weekplanExercise->update([
            'day' => 2,
            'start_time' => '11:00',
            'end_time' => '12:00',
            'intensity' => 'crimson',
        ]);

        // Refresh the model from the database
        $weekplanExercise = $weekplanExercise->fresh();

        $this->assertEquals(2, $weekplanExercise->day);
        $this->assertEquals('11:00', $weekplanExercise->start_time);
        $this->assertEquals('12:00', $weekplanExercise->end_time);
        $this->assertEquals('crimson', $weekplanExercise->intensity);
    }
}
