<?php

/**
 * Browser testing using Pest v4 Browser plugin.
 */
it('can render the login page in a browser', function () {
    $page = visit('/admin/login');
    $page->assertSee('Logg inn på konto');
})->group('browser');

it('can visit the admin page', function () {
    $page = visit('/admin/login');
    $page->assertSee('Logg inn');
})->group('browser');
