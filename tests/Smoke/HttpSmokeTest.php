<?php

/**
 * Smoke tests using Pest v4 Smoke plugin.
 */

use function Pest\Smoke\smoke;

smoke('home page')->get('/')->assertOk();
smoke('login page')->get('/login')->assertSuccessful();
smoke('artisan about')->artisan('about')->assertSuccessful();
