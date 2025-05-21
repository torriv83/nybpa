<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    $this->role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

    $this->user = User::factory()->create([
        'email' => 'test+'.uniqid().'@trivera.net',
        'email_verified_at' => now(),
    ]);

    $this->user->assignRole('Admin');
});

$urls = [
    '/privat/utstyrs',
    '/admin',
    '/admin/kalender',
    //    '/admin/timelister',
    '/privat/kategoris',
    '/privat/economies',
    '/privat/wishlists',
    '/landslag/exercises',
    '/landslag/test-results',
    '/landslag/tests',
    '/landslag/weekplans',
    '/admin/users',
    '/admin/roles',
    '/admin/permissions',
    '/admin/emails',
    // Add other URLs to test here
];

foreach ($urls as $url) {
    test("loads successfully for URL: {$url}", function () use ($url) {
        $response = $this->actingAs($this->user)->get($url);

        // Assert that the response was successful
        $response->assertSuccessful();
    });
}
