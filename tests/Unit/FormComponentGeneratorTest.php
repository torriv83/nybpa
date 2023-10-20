<?php

use App\Constants\Timesheet;
use App\Generators\FormComponentGenerator;
use App\Services\DateTimeService;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Get;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

it('generates a dynamic rich editor component correctly', function () {
    $mockedDateTimeService = Mockery::mock(DateTimeService::class);

    $formComponentGenerator = new FormComponentGenerator($mockedDateTimeService);

    $richEditorComponent = $formComponentGenerator->dynamicRichEditor('description', 'Test Label');

    expect($richEditorComponent[0])->toBeInstanceOf(RichEditor::class)
        ->and($richEditorComponent[0]->getName())->toBe('description')
        ->and($richEditorComponent[0]->getLabel())->toBe('Test Label');
});

it('generates a dynamic hidden field component correctly', function () {
    $mockedDateTimeService = Mockery::mock(DateTimeService::class);

    $formComponentGenerator = new FormComponentGenerator($mockedDateTimeService);

    $hiddenComponent = $formComponentGenerator->dynamicHidden('test_name', 'default_value');

    expect($hiddenComponent[0])->toBeInstanceOf(Hidden::class)
        ->and($hiddenComponent[0]->getName())->toBe('test_name')
        ->and($hiddenComponent[0]->getDefaultState())->toBe('default_value');
});

it('generates a dynamic date time picker component', function () {
    // Arrange
    $name = 'dateTimePicker';
    $label = 'Date Time Picker';
    $config = [
        'isAdmin' => true,
    ];
    $component = DateTimePicker::make($name)
        ->label($label)
        ->timezone('Europe/Oslo')
        ->suffixIcon('heroicon-o-clock')
        ->native(false)
        ->disabledDates(function (Get $get, DateTimeService $dateTimeService) {
            $recordId = $get('id');

            return $dateTimeService->getAllDisabledDates($get('user_id'), $recordId) ?? [];
        })
        ->formatStateUsing(
            fn ($state) => is_null($state)
                ? Carbon::now()->minute(floor(Carbon::now()->minute / 15) * 15)->second(0)->format('Y-m-d H:i')
                : $state
        )
        ->minDate(fn ($operation) => ($operation == 'edit' || $config['isAdmin'])
            ? null
            : Carbon::parse(today())->format('d.m.Y H:i'))
        ->minutesStep(Timesheet::MINUTES_STEP)
        ->seconds(false)
        ->required()
        ->live();

    // Act
    $result = FormComponentGenerator::dynamicDateTimePicker($name, $label, $config);

    // Assert
    expect($result)->toEqual([$component]);
});

it('tests the dynamicDatePicker method', function () {
    // Arrange
    $name = 'testDatePicker';
    $label = 'Test Date Picker';
    $config = ['isAdmin' => true];
    $component = DatePicker::make($name)
        ->label($label)
        ->native(false)
        ->disabledDates(function (Get $get, DateTimeService $dateTimeService) {
            $recordId = $get('id');
            return $dateTimeService->getAllDisabledDates($get('user_id'), $recordId) ?? [];
        })
        ->suffixIcon('calendar')
        ->displayFormat('d.m.Y')
        ->minDate(fn($operation) => ($operation == 'edit' || $config['isAdmin'])
            ? null
            : Carbon::parse(today())->format('d.m.Y'))
        ->live()
        ->required();

    // Act
    $result = FormComponentGenerator::dynamicDatePicker($name, $label, $config);

    // Assert
    expect($result)->toEqual([$component])
        ->and($result)->toBeArray()
        ->and($result)->toHaveCount(1)
        ->and($component)->toBeInstanceOf(DatePicker::class)
        ->and($component->getName())->toBe($name)
        ->and($component->getLabel())->toBe($label)
        ->and($component->getSuffixIcon())->toBe('calendar')
        ->and($component->getDisplayFormat())->toBe('d.m.Y')
        ->and($component->isLive())->toBeTrue()
        ->and($component->isRequired())->toBeTrue();

});
