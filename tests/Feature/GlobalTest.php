<?php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('if dump is being used')
    ->expect(['dd', 'dump', 'ds', 'ray'])
    ->not->toBeUsed();
