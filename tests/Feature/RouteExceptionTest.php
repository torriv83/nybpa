<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Create the "Admin" role
    $this->role = Role::create(['name' => 'Admin']);

    // Create the user
    $this->user = User::factory()->create([
        'email'             => 'test@trivera.net', // Adjust as needed
        'email_verified_at' => now(),
    ]);

    // Assign the role to the user
    $this->user->assignRole('Admin');
});

$urls = [
    '/admin/utstyrs',
    '/admin',
    '/admin/kalender',
//    '/admin/timelister',
    '/admin/kategoris',
    '/admin/economies',
    '/admin/wishlists',
    '/admin/days',
    '/admin/exercises',
    '/admin/test-results',
    '/admin/tests',
    '/admin/weekplans',
    '/admin/users',
    '/admin/roles',
    '/admin/permissions',
    // Add other URLs to test here
];

foreach ($urls as $url) {
    it("loads successfully for URL: {$url}", function () use ($url) {
        $response = $this->actingAs($this->user)->get($url);

        // Assert that the response was successful
        $response->assertSuccessful();
    });
}
