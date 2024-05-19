<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/*beforeEach(function () {
    // Create the "Admin" role
    $this->role = Role::create(['name' => 'Admin']);

    // Create the user
    $this->user = User::factory()->create([
        'email'             => 'test@trivera.net', // Adjust as needed
        'email_verified_at' => now(),
    ]);

    // Assign the role to the user
    $this->user->assignRole('Admin');
});*/

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
        $response = $this->get($url);

        // Assert that the response was successful
        $response->assertSuccessful();
    });
}
