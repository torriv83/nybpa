<?php

namespace Tests\Unit\Models;

use App\Models\TrainingProgram;
use App\Models\WorkoutExercise;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorkoutExerciseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_workout_exercise()
    {
        $workoutExercise = WorkoutExercise::create([
            'exercise_name' => 'Test Exercise',
        ]);

        $this->assertInstanceOf(WorkoutExercise::class, $workoutExercise);
        $this->assertEquals('Test Exercise', $workoutExercise->exercise_name);
    }

    #[Test]
    public function it_belongs_to_many_training_programs()
    {
        $workoutExercise = WorkoutExercise::create([
            'exercise_name' => 'Test Exercise',
        ]);

        $trainingProgram1 = TrainingProgram::create([
            'program_name' => 'Program 1',
        ]);

        $trainingProgram2 = TrainingProgram::create([
            'program_name' => 'Program 2',
        ]);

        // Attach training programs to the workout exercise with pivot data
        $workoutExercise->TrainingPrograms()->attach($trainingProgram1->id, [
            'repetitions' => 10,
            'sets' => 3,
            'order' => 1,
            'rest' => '60 seconds',
            'description' => 'First exercise',
        ]);

        $workoutExercise->TrainingPrograms()->attach($trainingProgram2->id, [
            'repetitions' => 12,
            'sets' => 4,
            'order' => 2,
            'rest' => '90 seconds',
            'description' => 'Second exercise',
        ]);

        $this->assertInstanceOf(Collection::class, $workoutExercise->TrainingPrograms);
        $this->assertCount(2, $workoutExercise->TrainingPrograms);
        $this->assertInstanceOf(TrainingProgram::class, $workoutExercise->TrainingPrograms->first());

        // Check pivot data
        $pivot = $workoutExercise->TrainingPrograms->first()->pivot;
        $this->assertEquals(10, $pivot->repetitions);
        $this->assertEquals(3, $pivot->sets);
        $this->assertEquals(1, $pivot->order);
        $this->assertEquals('60 seconds', $pivot->rest);
        $this->assertEquals('First exercise', $pivot->description);
    }

    #[Test]
    public function it_can_update_a_workout_exercise()
    {
        $workoutExercise = WorkoutExercise::create([
            'exercise_name' => 'Original Exercise',
        ]);

        // Update the workout exercise
        $workoutExercise->update([
            'exercise_name' => 'Updated Exercise',
        ]);

        // Refresh the model from the database
        $workoutExercise = $workoutExercise->fresh();

        $this->assertEquals('Updated Exercise', $workoutExercise->exercise_name);
    }

    #[Test]
    public function it_uses_soft_deletes()
    {
        // Check if the model uses SoftDeletes trait
        $this->assertContains(SoftDeletes::class, class_uses_recursive(WorkoutExercise::class));

        $workoutExercise = WorkoutExercise::create([
            'exercise_name' => 'Test Exercise',
        ]);

        $workoutExerciseId = $workoutExercise->id;

        // Delete the workout exercise
        $workoutExercise->delete();

        // The workout exercise should still exist in the database
        $this->assertDatabaseHas('workout_exercises', ['id' => $workoutExerciseId]);

        // But should not be retrieved in a normal query
        $this->assertNull(WorkoutExercise::find($workoutExerciseId));

        // Should be retrieved when including trashed
        $this->assertNotNull(WorkoutExercise::withTrashed()->find($workoutExerciseId));
    }

    #[Test]
    public function it_can_sync_training_programs()
    {
        $workoutExercise = WorkoutExercise::create([
            'exercise_name' => 'Test Exercise',
        ]);

        $trainingProgram1 = TrainingProgram::create([
            'program_name' => 'Program 1',
        ]);

        $trainingProgram2 = TrainingProgram::create([
            'program_name' => 'Program 2',
        ]);

        $trainingProgram3 = TrainingProgram::create([
            'program_name' => 'Program 3',
        ]);

        // Attach initial training programs
        $workoutExercise->TrainingPrograms()->attach([
            $trainingProgram1->id => [
                'repetitions' => 10,
                'sets' => 3,
                'order' => 1,
                'rest' => '60 seconds',
                'description' => 'First exercise',
            ],
            $trainingProgram2->id => [
                'repetitions' => 12,
                'sets' => 4,
                'order' => 2,
                'rest' => '90 seconds',
                'description' => 'Second exercise',
            ],
        ]);

        // Sync with new training programs (removing program 1, keeping program 2, adding program 3)
        $workoutExercise->TrainingPrograms()->sync([
            $trainingProgram2->id => [
                'repetitions' => 15,
                'sets' => 5,
                'order' => 1,
                'rest' => '120 seconds',
                'description' => 'Updated exercise',
            ],
            $trainingProgram3->id => [
                'repetitions' => 8,
                'sets' => 2,
                'order' => 2,
                'rest' => '45 seconds',
                'description' => 'New exercise',
            ],
        ]);

        // Refresh the model to get updated relationships
        $workoutExercise = $workoutExercise->fresh();

        $this->assertCount(2, $workoutExercise->TrainingPrograms);

        // Program 1 should be detached
        $this->assertFalse($workoutExercise->TrainingPrograms->contains($trainingProgram1));

        // Program 2 should be updated
        $program2 = $workoutExercise->TrainingPrograms->where('id', $trainingProgram2->id)->first();
        $this->assertNotNull($program2);
        $this->assertEquals(15, $program2->pivot->repetitions);
        $this->assertEquals(5, $program2->pivot->sets);
        $this->assertEquals('Updated exercise', $program2->pivot->description);

        // Program 3 should be attached
        $program3 = $workoutExercise->TrainingPrograms->where('id', $trainingProgram3->id)->first();
        $this->assertNotNull($program3);
        $this->assertEquals(8, $program3->pivot->repetitions);
        $this->assertEquals(2, $program3->pivot->sets);
        $this->assertEquals('New exercise', $program3->pivot->description);
    }
}
