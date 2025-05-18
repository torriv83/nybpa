<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Create the "Admin" role
        $this->role = Role::create(['name' => 'Admin']);

        // Create the user
        $this->user = User::factory()->create([
            'email' => 'test@trivera.net', // Adjust as needed
            'email_verified_at' => now(),
        ]);

        // Assign the role to the user
        $this->user->assignRole('Admin');

        $this->actingAs($this->user);
    }
}
