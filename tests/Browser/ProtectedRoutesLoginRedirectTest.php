<?php

/**
 * Browser coverage for protected routes using Pest Browser plugin.
 * We expect unauthenticated users to be presented with a login screen.
 */
$adminRoutes = [
    '/admin',
    '/admin/kalender',
    '/admin/innstillinger',
    '/admin/users',
    '/admin/roles',
    '/admin/permissions',
    '/admin/emails',
    '/admin/backups',
];

$privatRoutes = [
    '/privat/utstyrs',
    '/privat/kategoris',
    '/privat/economies',
    '/privat/wishlists',
    '/privat/resepters',
    '/privat/emails',
];

$landslagRoutes = [
    '/landslag/exercises',
    '/landslag/test-results',
    '/landslag/tests',
    '/landslag/weekplans',
    '/landslag/training-programs',
    '/landslag/workout-exercises',
];

$assistentRoutes = [
    '/assistent',
    '/assistent/timesheets',
];

$allRoutes = array_merge($adminRoutes, $privatRoutes, $landslagRoutes, $assistentRoutes);

it('renders a login screen for protected areas (no server error)', function () use ($allRoutes) {
    foreach ($allRoutes as $url) {
        $page = visit($url);
        $page->assertSee('Logg inn på konto');
    }
})->group('browser');
