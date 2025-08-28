<?php

/**
 * Browser testing using Pest v4 Browser plugin.
 * These tests open a real browser (via Symfony Panther) and interact with your app.
 */

use Pest\Browser\Browser;

use function Pest\Browser\browse;

it('can render the login page in a browser', function () {
    browse(function (Browser $browser) {
        $browser->visit('/login')
            ->assertSeeIn('body', 'Login');
    });
})->group('browser');

it('can visit the home page', function () {
    browse(function (Browser $browser) {
        $browser->visit('/')
            ->assertStatus(200);
    });
})->group('browser');
