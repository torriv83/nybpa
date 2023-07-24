<?php

test('globals')
    ->expect(['dd', 'dump', 'ds'])
    ->not->toBeUsed();
