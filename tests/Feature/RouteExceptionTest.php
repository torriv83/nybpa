<?php

/**
 * Route Exception Test
 *
 * This test ensures that all routes in the application load successfully.
 *
 * How to find all routes:
 * - Run 'php artisan route:list' to get a complete list of all routes in the application
 * - Add new routes to the appropriate dataset below
 */
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

// Group routes by section for better organization
dataset('adminRoutes', [
    'dashboard' => '/admin',
    'calendar' => '/admin/kalender',
    'settings' => '/admin/innstillinger',
    'users' => '/admin/users',
    'roles' => '/admin/roles',
    'permissions' => '/admin/permissions',
    'emails' => '/admin/emails',
    'backups' => '/admin/backups',
    // Auth and profile routes are typically not tested in this way
    // 'profile' => '/admin/profile',
]);

dataset('privatRoutes', [
    'equipment' => '/privat/utstyrs',
    'categories' => '/privat/kategoris',
    'economies' => '/privat/economies',
    'wishlists' => '/privat/wishlists',
    'recipes' => '/privat/resepters',
    'emails' => '/privat/emails',
    // Auth and profile routes are typically not tested in this way
    // 'dashboard' => '/privat',
]);

dataset('landslagRoutes', [
    'exercises' => '/landslag/exercises',
    'test-results' => '/landslag/test-results',
    'tests' => '/landslag/tests',
    'weekplans' => '/landslag/weekplans',
    'training-programs' => '/landslag/training-programs',
    'workout-exercises' => '/landslag/workout-exercises',
    // Auth and profile routes are typically not tested in this way
    // 'dashboard' => '/landslag',
]);

dataset('assistentRoutes', [
    'dashboard' => '/assistent',
    'timesheets' => '/assistent/timesheets',
    // Auth and profile routes are typically not tested in this way
    // 'profile' => '/assistent/profile',
]);

// Test admin routes
test('admin routes load successfully', function (string $url) {
    $response = $this->actingAs($this->user)->get($url);

    // Assert that the response was successful
    $response->assertSuccessful();
})->with('adminRoutes');

// Test privat routes
test('privat routes load successfully', function (string $url) {
    $response = $this->actingAs($this->user)->get($url);

    // Assert that the response was successful
    $response->assertSuccessful();
})->with('privatRoutes');

// Test landslag routes
test('landslag routes load successfully', function (string $url) {
    $response = $this->actingAs($this->user)->get($url);

    // Assert that the response was successful
    $response->assertSuccessful();
})->with('landslagRoutes');

// Test assistent routes
test('assistent routes load successfully', function (string $url) {
    $response = $this->actingAs($this->user)->get($url);

    // Assert that the response was successful
    $response->assertSuccessful();
})->with('assistentRoutes');
