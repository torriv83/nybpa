<?php

namespace Tests\Unit\Models;

use App\Models\TrainingProgram;
use App\Models\WorkoutExercise;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrainingProgramTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_training_program()
    {
        $trainingProgram = TrainingProgram::create([
            'program_name' => 'Test Program',
            'description' => 'Test Description',
        ]);

        $this->assertInstanceOf(TrainingProgram::class, $trainingProgram);
        $this->assertEquals('Test Program', $trainingProgram->program_name);
        $this->assertEquals('Test Description', $trainingProgram->description);
    }

    #[Test]
    public function it_belongs_to_many_workout_exercises()
    {
        $trainingProgram = TrainingProgram::create([
            'program_name' => 'Test Program',
            'description' => 'Test Description',
        ]);

        $workoutExercise1 = WorkoutExercise::create([
            'exercise_name' => 'Exercise 1',
        ]);

        $workoutExercise2 = WorkoutExercise::create([
            'exercise_name' => 'Exercise 2',
        ]);

        // Attach workout exercises to the training program with pivot data
        $trainingProgram->WorkoutExercises()->attach($workoutExercise1->id, [
            'repetitions' => 10,
            'sets' => 3,
            'order' => 1,
            'rest' => '60 seconds',
            'description' => 'First exercise',
        ]);

        $trainingProgram->WorkoutExercises()->attach($workoutExercise2->id, [
            'repetitions' => 12,
            'sets' => 4,
            'order' => 2,
            'rest' => '90 seconds',
            'description' => 'Second exercise',
        ]);

        $this->assertInstanceOf(Collection::class, $trainingProgram->WorkoutExercises);
        $this->assertCount(2, $trainingProgram->WorkoutExercises);
        $this->assertInstanceOf(WorkoutExercise::class, $trainingProgram->WorkoutExercises->first());

        // Check pivot data
        $pivot = $trainingProgram->WorkoutExercises->first()->pivot;
        $this->assertEquals(10, $pivot->repetitions);
        $this->assertEquals(3, $pivot->sets);
        $this->assertEquals(1, $pivot->order);
        $this->assertEquals('60 seconds', $pivot->rest);
        $this->assertEquals('First exercise', $pivot->description);
    }

    #[Test]
    public function it_can_update_a_training_program()
    {
        $trainingProgram = TrainingProgram::create([
            'program_name' => 'Original Program',
            'description' => 'Original Description',
        ]);

        // Update the training program
        $trainingProgram->update([
            'program_name' => 'Updated Program',
            'description' => 'Updated Description',
        ]);

        // Refresh the model from the database
        $trainingProgram = $trainingProgram->fresh();

        $this->assertEquals('Updated Program', $trainingProgram->program_name);
        $this->assertEquals('Updated Description', $trainingProgram->description);
    }

    #[Test]
    public function it_uses_soft_deletes()
    {
        // Check if the model uses SoftDeletes trait
        $this->assertContains(SoftDeletes::class, class_uses_recursive(TrainingProgram::class));

        $trainingProgram = TrainingProgram::create([
            'program_name' => 'Test Program',
            'description' => 'Test Description',
        ]);

        $trainingProgramId = $trainingProgram->id;

        // Delete the training program
        $trainingProgram->delete();

        // The training program should still exist in the database
        $this->assertDatabaseHas('training_programs', ['id' => $trainingProgramId]);

        // But should not be retrieved in a normal query
        $this->assertNull(TrainingProgram::find($trainingProgramId));

        // Should be retrieved when including trashed
        $this->assertNotNull(TrainingProgram::withTrashed()->find($trainingProgramId));
    }

    #[Test]
    public function it_can_sync_workout_exercises()
    {
        $trainingProgram = TrainingProgram::create([
            'program_name' => 'Test Program',
            'description' => 'Test Description',
        ]);

        $workoutExercise1 = WorkoutExercise::create([
            'exercise_name' => 'Exercise 1',
        ]);

        $workoutExercise2 = WorkoutExercise::create([
            'exercise_name' => 'Exercise 2',
        ]);

        $workoutExercise3 = WorkoutExercise::create([
            'exercise_name' => 'Exercise 3',
        ]);

        // Attach initial workout exercises
        $trainingProgram->WorkoutExercises()->attach([
            $workoutExercise1->id => [
                'repetitions' => 10,
                'sets' => 3,
                'order' => 1,
                'rest' => '60 seconds',
                'description' => 'First exercise',
            ],
            $workoutExercise2->id => [
                'repetitions' => 12,
                'sets' => 4,
                'order' => 2,
                'rest' => '90 seconds',
                'description' => 'Second exercise',
            ],
        ]);

        // Sync with new workout exercises (removing exercise 1, keeping exercise 2, adding exercise 3)
        $trainingProgram->WorkoutExercises()->sync([
            $workoutExercise2->id => [
                'repetitions' => 15,
                'sets' => 5,
                'order' => 1,
                'rest' => '120 seconds',
                'description' => 'Updated exercise',
            ],
            $workoutExercise3->id => [
                'repetitions' => 8,
                'sets' => 2,
                'order' => 2,
                'rest' => '45 seconds',
                'description' => 'New exercise',
            ],
        ]);

        // Refresh the model to get updated relationships
        $trainingProgram = $trainingProgram->fresh();

        $this->assertCount(2, $trainingProgram->WorkoutExercises);

        // Exercise 1 should be detached
        $this->assertFalse($trainingProgram->WorkoutExercises->contains($workoutExercise1));

        // Exercise 2 should be updated
        $exercise2 = $trainingProgram->WorkoutExercises->where('id', $workoutExercise2->id)->first();
        $this->assertNotNull($exercise2);
        $this->assertEquals(15, $exercise2->pivot->repetitions);
        $this->assertEquals(5, $exercise2->pivot->sets);
        $this->assertEquals('Updated exercise', $exercise2->pivot->description);

        // Exercise 3 should be attached
        $exercise3 = $trainingProgram->WorkoutExercises->where('id', $workoutExercise3->id)->first();
        $this->assertNotNull($exercise3);
        $this->assertEquals(8, $exercise3->pivot->repetitions);
        $this->assertEquals(2, $exercise3->pivot->sets);
        $this->assertEquals('New exercise', $exercise3->pivot->description);
    }
}
