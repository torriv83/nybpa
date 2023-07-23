<?php

test('if debugbar is on', function () {
    expect(config('debugbar.enabled'))->toBeFalse();
});
