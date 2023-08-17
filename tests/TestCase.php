<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /*    protected function setUp(): void
        {
            parent::setUp();

            $this->actingAs(User::factory()->create([
                'email'             => fake()->name . '@trivera.net',
                'email_verified_at' => now(),
            ]));
        }*/
}
