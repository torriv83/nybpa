<?php

namespace Tests\Unit\Models;

use App\Models\Economy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EconomyTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_an_economy_record()
    {
        $economy = Economy::create([
            'before_tax' => 50000,
            'after_tax' => 35000,
            'tax_table' => 'standard',
            'grunnstonad' => 1500,
        ]);

        $this->assertInstanceOf(Economy::class, $economy);
        $this->assertEquals(50000, $economy->before_tax);
        $this->assertEquals(35000, $economy->after_tax);
        $this->assertEquals('standard', $economy->tax_table);
        $this->assertEquals(1500, $economy->grunnstonad);
    }

    #[Test]
    public function it_uses_the_correct_table_name()
    {
        $economy = new Economy;

        $this->assertEquals('economy', $economy->getTable());
    }

    #[Test]
    public function it_uses_timestamps()
    {
        $economy = Economy::create([
            'before_tax' => 50000,
            'after_tax' => 35000,
            'tax_table' => '1205',
            'grunnstonad' => 1500,
        ]);

        $this->assertNotNull($economy->created_at);
        $this->assertNotNull($economy->updated_at);
    }

    #[Test]
    public function it_uses_soft_deletes()
    {
        $economy = Economy::create([
            'before_tax' => 50000,
            'after_tax' => 35000,
            'tax_table' => '1205',
            'grunnstonad' => 1500,
        ]);

        $economyId = $economy->id;

        // Delete the economy record
        $economy->delete();

        // The economy record should still exist in the database
        $this->assertDatabaseHas('economy', ['id' => $economyId]);

        // But should not be retrieved in a normal query
        $this->assertNull(Economy::find($economyId));

        // Should be retrieved when including trashed
        $this->assertNotNull(Economy::withTrashed()->find($economyId));
    }

    #[Test]
    public function it_can_update_an_economy_record()
    {
        $economy = Economy::create([
            'before_tax' => 50000,
            'after_tax' => 35000,
            'tax_table' => 'standard',
            'grunnstonad' => 1500,
        ]);

        // Update the economy record
        $economy->update([
            'before_tax' => 55000,
            'after_tax' => 38000,
        ]);

        // Refresh the model from the database
        $economy = $economy->fresh();

        $this->assertEquals(55000, $economy->before_tax);
        $this->assertEquals(38000, $economy->after_tax);
        $this->assertEquals('standard', $economy->tax_table); // Unchanged
        $this->assertEquals(1500, $economy->grunnstonad); // Unchanged
    }
}
