<?php

use App\Constants\Timesheet;
use App\Testing\DateAndTimeHelperTestingClass;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('checks the common fields for non-admin', function () {
    $helperUser = new DateAndTimeHelperTestingClass();
    $commonFields = $helperUser->getCommonFields(false);

    $foundFraDatoTime = false;
    $foundTilDatoTime = false;
    foreach ($commonFields as $field) {
        if ($field->getName() === Timesheet::FRA_DATO_TIME) {
            $foundFraDatoTime = true;
        }
        if ($field->getName() === Timesheet::TIL_DATO_TIME) {
            $foundTilDatoTime = true;
        }
    }

    expect($foundFraDatoTime)->toBeTrue()
        ->and($foundTilDatoTime)->toBeTrue();
});

it('checks the common fields for admin', function () {
    $helperUser = new DateAndTimeHelperTestingClass();
    $commonFields = $helperUser->getCommonFields(true);

    $foundFraDatoTime = false;
    $foundTilDatoTime = false;
    foreach ($commonFields as $field) {
        if ($field->getName() === Timesheet::FRA_DATO_TIME) {
            $foundFraDatoTime = true;
        }
        if ($field->getName() === Timesheet::TIL_DATO_TIME) {
            $foundTilDatoTime = true;
        }
    }

    expect($foundFraDatoTime)->toBeTrue()
        ->and($foundTilDatoTime)->toBeTrue();
});
