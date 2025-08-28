<?php

/**
 * Expanded Smoke tests using Pest v4 Smoke plugin.
 */

use function Pest\Smoke\smoke;

smoke('admin login')->get('/admin/login')->assertSuccessful();
smoke('root')->get('/')->assertOk();
smoke('login (generic)')->get('/login')->assertSuccessful();
smoke('artisan about')->artisan('about')->assertSuccessful();
smoke('artisan route:list')->artisan('route:list')->assertSuccessful();
