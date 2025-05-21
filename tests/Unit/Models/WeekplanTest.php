<?php

namespace Tests\Unit\Models;

use App\Models\Exercise;
use App\Models\Weekplan;
use App\Models\WeekplanExercise;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WeekplanTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_weekplan()
    {
        $weekplan = Weekplan::create([
            'name' => 'Test Weekplan',
        ]);

        $this->assertInstanceOf(Weekplan::class, $weekplan);
        $this->assertEquals('Test Weekplan', $weekplan->name);
    }

    #[Test]
    public function it_has_many_weekplan_exercises()
    {
        // Create exercises first
        $exercise1 = Exercise::create(['name' => 'Exercise 1']);
        $exercise2 = Exercise::create(['name' => 'Exercise 2']);

        $weekplan = Weekplan::create([
            'name' => 'Test Weekplan',
        ]);

        // Create some weekplan exercises for the weekplan
        WeekplanExercise::create([
            'weekplan_id' => $weekplan->id,
            'exercise_id' => $exercise1->id,
            'day' => 1,
            'start_time' => '09:00',
            'end_time' => '10:00',
            'intensity' => 'green',
        ]);

        WeekplanExercise::create([
            'weekplan_id' => $weekplan->id,
            'exercise_id' => $exercise2->id,
            'day' => 2,
            'start_time' => '14:00',
            'end_time' => '15:00',
            'intensity' => 'crimson',
        ]);

        $this->assertInstanceOf(Collection::class, $weekplan->weekplanExercises);
        $this->assertCount(2, $weekplan->weekplanExercises);
        $this->assertInstanceOf(WeekplanExercise::class, $weekplan->weekplanExercises->first());
    }

    #[Test]
    public function it_can_scope_to_active_weekplans()
    {
        // Create an active weekplan
        $activeWeekplan = Weekplan::create([
            'name' => 'Active Weekplan',
        ]);

        // Set is_active to 1 manually since it's not in the fillable array
        $activeWeekplan->is_active = 1;
        $activeWeekplan->save();

        // Create an inactive weekplan
        $inactiveWeekplan = Weekplan::create([
            'name' => 'Inactive Weekplan',
        ]);

        // Set is_active to 0 manually
        $inactiveWeekplan->is_active = 0;
        $inactiveWeekplan->save();

        // Test the active scope
        $activeWeekplans = Weekplan::active()->get();

        $this->assertCount(1, $activeWeekplans);
        $this->assertEquals('Active Weekplan', $activeWeekplans->first()->name);
    }

    #[Test]
    public function it_uses_soft_deletes()
    {
        // Check if the model uses SoftDeletes trait
        $this->assertContains(SoftDeletes::class, class_uses_recursive(Weekplan::class));

        $weekplan = Weekplan::create([
            'name' => 'Test Weekplan',
        ]);

        $weekplanId = $weekplan->id;

        // Delete the weekplan
        $weekplan->delete();

        // The weekplan should still exist in the database
        $this->assertDatabaseHas('weekplans', ['id' => $weekplanId]);

        // But should not be retrieved in a normal query
        $this->assertNull(Weekplan::find($weekplanId));

        // Should be retrieved when including trashed
        $this->assertNotNull(Weekplan::withTrashed()->find($weekplanId));
    }

    #[Test]
    public function it_can_update_a_weekplan()
    {
        $weekplan = Weekplan::create([
            'name' => 'Original Name',
        ]);

        // Update the weekplan
        $weekplan->update([
            'name' => 'Updated Name',
        ]);

        // Refresh the model from the database
        $weekplan = $weekplan->fresh();

        $this->assertEquals('Updated Name', $weekplan->name);
    }
}
