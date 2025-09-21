# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Laravel Filament Application

This is a Laravel 12 application using Filament v3 for the admin interface. The application manages timesheets, training programs, medical equipment, and personal finances across multiple user panels.

## Multi-Panel Architecture

The application uses Filament's multi-panel system with distinct user roles:

- **Admin Panel** (`app/Filament/Admin/`) - Full administrative access for timesheets, users, and system settings
- **Assistent Panel** (`app/Filament/Assistent/`) - Assistant-level access for timesheet management
- **Landslag Panel** (`app/Filament/Landslag/`) - Training program management, exercises, test results
- **Privat Panel** (`app/Filament/Privat/`) - Personal management for economy, medical equipment, prescriptions, wishlists

## Key Development Commands

### Testing
- `php artisan test` or `./vendor/bin/pest` - Run all tests using Pest
- `php artisan test --filter=TestName` - Run specific test

### Code Analysis
- `composer phpstan` or `./vendor/bin/phpstan analyse` - Run PHPStan static analysis (level 6)
- `./vendor/bin/pint` - Fix code styling with Laravel Pint

### Laravel Commands
- `php artisan migrate` - Run database migrations
- `php artisan cache:clear` - Clear application cache
- `npm run cc` - Clear all caches (cache, view, config, event, route)
- `php artisan filament:upgrade` - Upgrade Filament components

### Asset Development
- `npm run dev` - Start Vite development server
- `npm run build` - Build assets for production
- `npm run watch` - Watch for asset changes

## Cache Management System

This application implements a sophisticated cache tagging system documented in `CACHE_TAGS.md`:

- **timesheet** - Caches working time data (1 week TTL)
- **testresult** - Caches test/fitness data (1 month TTL)  
- **settings** - Caches user preferences (1 month TTL)
- **medisinsk** - Caches medical equipment data
- **bruker** - Caches user data (1 month TTL)

When modifying data, ensure cache invalidation is triggered in the appropriate Resource pages.

## Core Models and Relationships

- **User** - Central user model with role-based panel access
- **Timesheet** - Work time tracking with calendar integration
- **TrainingProgram** - Exercise programs with related WorkoutExercises
- **TestResults** - Fitness test data with chart widgets
- **Economy** - Financial tracking with YNAB integration
- **Medical Models** - Kategori, Utstyr, Resepter for equipment and prescriptions

## Testing Strategy

Uses Pest for testing with:
- **Unit Tests** - Model logic, services, and utilities
- **Feature Tests** - Authentication, routes, and integration
- **Architecture Tests** - Code quality and structure enforcement

Test environment configured with SQLite in-memory database and array caching.

## Important Notes

- **PHPStan Level 6** - Maintain strict type checking standards
- **Multi-tenant Architecture** - Each panel operates independently with role-based access
- **Cache Invalidation** - Always invalidate relevant cache tags when updating data
- **Filament Resources** - Follow established patterns for Pages, Widgets, and RelationManagers