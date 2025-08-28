<?php

/**
 * Visual regression testing with Pest v4 Visual plugin (integrated with Browser plugin).
 */

use Pest\Browser\Browser;

use function Pest\Browser\browse;

it('login page visual snapshot matches', function () {
    browse(function (Browser $browser) {
        $browser->visit('/login')
            ->assertSeeIn('body', 'Login')
            // Update baseline: set PEST_UPDATE_SNAPSHOTS=1 when running the test
            ->assertMatchesScreenshot('login-page');
    });
})->group('visual');
