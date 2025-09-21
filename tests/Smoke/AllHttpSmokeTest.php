<?php

test('No Smoke errors', function () {
    $routes = ['/', '/admin/login', '/contact'];

    visit($routes)->assertNoSmoke();

});

// assertNoSmoke() is a shorthand for:
// - assertNoJavascriptErrors()
// - assertNoConsoleLogs()
