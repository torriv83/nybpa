<?php

test('if dump is being used')
    ->expect(['dd', 'dump', 'ds'])
    ->not->toBeUsed();

test('if debugbar is on', function () {
    expect(config('debugbar.enabled'))->toBeFalse();
});

it('does not contain deprecated code', function () {
    $directory = new RecursiveDirectoryIterator(app_path());
    $iterator  = new RecursiveIteratorIterator($directory);
    $files     = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

    // Define the patterns you want to check for
    $patterns = [
        'Card::make',
        'use Filament\Pages\Actions;',
    ];

    $failures = [];

    foreach ($files as $file) {
        $code = file_get_contents($file[0]);
        foreach ($patterns as $pattern) {
            if (strpos($code, $pattern) !== false) {
                $failures[] = "Pattern '{$pattern}' found in {$file[0]}";
            }
        }
    }

    $failureMessage = "Deprecated code found:\n" . implode("\n", $failures);

    PHPUnit\Framework\Assert::assertCount(
        0,
        $failures,
        $failureMessage
    );
});
