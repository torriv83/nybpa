<?php

test('if dump is being used')
    ->expect(['dd', 'dump', 'ds'])
    ->not->toBeUsed();

test('if debugbar is on', function () {
    expect(config('debugbar.enabled'))->toBeFalse();
});
