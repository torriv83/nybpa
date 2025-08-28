<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class SmokeTestCase extends BaseTestCase
{
    use CreatesApplication;

    // Intentionally no setUp override to avoid DB/role/user bootstrapping.
}
