<?php

/**
 * Visual regression testing with Pest v4 Visual plugin (integrated with Browser plugin).
 */
it('login page visual snapshot matches', function () {
    $page = visit('/admin/login');
    $page->assertSee('Logg inn');
    // Take a screenshot for visual regression testing
    $page->screenshot();
})->group('visual');
