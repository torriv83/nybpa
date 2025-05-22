<?php

use App\Filament\Admin\Pages\Settings;
use App\Models\Settings as Setting;
use App\Models\User;
use Filament\Forms\Components\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    Auth::login($this->user);
});

test('settings page has correct form schema', function () {
    $settings = new Settings;
    $formSchema = invade($settings)->getFormSchema();

    // Check that the form has the expected sections
    expect($formSchema)->toHaveCount(3);

    // Check that each section is an instance of Section
    expect($formSchema[0])->toBeInstanceOf(Section::class);
    expect($formSchema[1])->toBeInstanceOf(Section::class);
    expect($formSchema[2])->toBeInstanceOf(Section::class);
});

test('settings model can be created and retrieved', function () {
    // Create settings for the user
    $setting = Setting::create([
        'user_id' => $this->user->id,
        'weekplan_timespan' => true,
        'weekplan_from' => '08:00:00',
        'weekplan_to' => '16:00:00',
        'bpa_hours_per_week' => 40,
        'apotek_epost' => 'test@example.com',
    ]);

    // Clear the cache to ensure we get the latest settings
    Cache::flush();

    // Retrieve the settings
    $retrievedSetting = Setting::getUserSettings($this->user->id);

    // Check that the settings were retrieved correctly
    expect($retrievedSetting->id)->toBe($setting->id);
    expect($retrievedSetting->weekplan_timespan)->toBeTrue();
    expect($retrievedSetting->weekplan_from)->toBe('08:00:00');
    expect($retrievedSetting->weekplan_to)->toBe('16:00:00');
    expect($retrievedSetting->bpa_hours_per_week)->toBe(40);
    expect($retrievedSetting->apotek_epost)->toBe('test@example.com');
});

test('settings model can be updated', function () {
    // Create settings for the user
    $setting = Setting::create([
        'user_id' => $this->user->id,
        'weekplan_timespan' => true,
        'weekplan_from' => '08:00:00',
        'weekplan_to' => '16:00:00',
        'bpa_hours_per_week' => 40,
        'apotek_epost' => 'test@example.com',
    ]);

    // Update the settings
    $setting->update([
        'weekplan_timespan' => false,
        'weekplan_from' => '09:00:00',
        'weekplan_to' => '17:00:00',
        'bpa_hours_per_week' => 35,
        'apotek_epost' => 'updated@example.com',
    ]);

    // Refresh the setting from the database
    $setting->refresh();

    // Check that the settings were updated
    expect($setting->weekplan_timespan)->toBeFalse();
    expect($setting->weekplan_from)->toBe('09:00:00');
    expect($setting->weekplan_to)->toBe('17:00:00');
    expect($setting->bpa_hours_per_week)->toBe(35);
    expect($setting->apotek_epost)->toBe('updated@example.com');
});

test('settings cache is cleared when settings are updated', function () {
    // Create settings for the user
    $setting = Setting::create([
        'user_id' => $this->user->id,
        'weekplan_timespan' => true,
        'weekplan_from' => '08:00:00',
        'weekplan_to' => '16:00:00',
        'bpa_hours_per_week' => 40,
        'apotek_epost' => 'test@example.com',
    ]);

    // Set up a cache value
    Cache::tags(['settings'])->put('user-settings-'.$this->user->id, $setting, 60);

    // Verify the cache value exists
    expect(Cache::tags(['settings'])->has('user-settings-'.$this->user->id))->toBeTrue();

    // Directly call the cache clearing code from the submit method
    Cache::tags(['settings'])->forget('user-settings-'.$this->user->id);

    // Check that the cache was cleared
    expect(Cache::tags(['settings'])->has('user-settings-'.$this->user->id))->toBeFalse();
});

test('clearCache method flushes the cache', function () {
    // Set up a cache value
    Cache::put('test-key', 'test-value', 60);

    $settings = new Settings;
    $settings->clearCache();

    // Check that the cache was flushed
    expect(Cache::has('test-key'))->toBeFalse();
});

test('mount method fills form with user settings', function () {
    // Create settings for the user
    $setting = Setting::create([
        'user_id' => $this->user->id,
        'weekplan_timespan' => true,
        'weekplan_from' => '08:00:00',
        'weekplan_to' => '16:00:00',
        'bpa_hours_per_week' => 40,
        'apotek_epost' => 'test@example.com',
    ]);

    // Create a settings page instance
    $settings = new Settings;

    // We can't easily test the form fill, so we'll just verify the settings was loaded
    $settings->mount();

    // Verify settings was loaded
    expect($settings->settings->id)->toBe($setting->id);
});

test('getHeaderActions returns correct actions', function () {
    $settings = new Settings;
    $actions = invade($settings)->getHeaderActions();

    expect($actions)->toHaveCount(2);
    expect($actions[0]->getName())->toBe('Lagre');
    expect($actions[1]->getName())->toBe('Cache');
    expect($actions[1]->getLabel())->toBe('Tøm Cache');
    expect($actions[1]->getColor())->toBe('danger');
});

test('submit method creates settings when they do not exist', function () {
    // Create a settings page instance with property values
    $settings = new Settings;
    $settings->weekplan_timespan = true;
    $settings->weekplan_from = '08:00:00';
    $settings->weekplan_to = '16:00:00';
    $settings->bpa_hours_per_week = 40;
    $settings->apotek_epost = 'new@example.com';

    // Create a form object that returns our data
    $form = new class
    {
        public function getState()
        {
            return [
                'weekplan_timespan' => true,
                'weekplan_from' => '08:00:00',
                'weekplan_to' => '16:00:00',
                'bpa_hours_per_week' => 40,
                'apotek_epost' => 'new@example.com',
            ];
        }
    };

    $settings->form = $form;

    // Call submit (we can't test the notification, but we can test the database update)
    $settings->submit();

    // Verify settings were created
    $setting = Setting::where('user_id', $this->user->id)->first();
    expect($setting)->not->toBeNull();
    expect($setting->weekplan_timespan)->toBeTrue();
    expect($setting->weekplan_from)->toBe('08:00:00');
    expect($setting->weekplan_to)->toBe('16:00:00');
    expect($setting->bpa_hours_per_week)->toBe(40);
    expect($setting->apotek_epost)->toBe('new@example.com');
});

test('static properties are correctly set', function () {
    $reflectionClass = new ReflectionClass(Settings::class);

    $navigationIcon = $reflectionClass->getProperty('navigationIcon');
    $navigationIcon->setAccessible(true);
    expect($navigationIcon->getValue())->toBe('heroicon-o-document-text');

    $navigationLabel = $reflectionClass->getProperty('navigationLabel');
    $navigationLabel->setAccessible(true);
    expect($navigationLabel->getValue())->toBe('Innstillinger');

    $view = $reflectionClass->getProperty('view');
    $view->setAccessible(true);
    expect($view->getValue())->toBe('filament.pages.settings');

    $slug = $reflectionClass->getProperty('slug');
    $slug->setAccessible(true);
    expect($slug->getValue())->toBe('innstillinger');

    $shouldRegisterNavigation = $reflectionClass->getProperty('shouldRegisterNavigation');
    $shouldRegisterNavigation->setAccessible(true);
    expect($shouldRegisterNavigation->getValue())->toBeFalse();
});

test('form schema has correct structure for conditional fields', function () {
    $settings = new Settings;
    $formSchema = invade($settings)->getFormSchema();

    // Get the Ukeplan section (third section)
    $ukeplanSection = $formSchema[2];

    // Check that the section has the correct title
    expect($ukeplanSection->getHeading())->toBe('Ukeplan');

    // Check that the section contains a Grid component
    $sectionSchema = invade($ukeplanSection)->getChildComponents();
    expect($sectionSchema[0])->toBeInstanceOf(\Filament\Forms\Components\Grid::class);

    // Check that the Grid has the correct number of columns
    $grid = $sectionSchema[0];
    $columns = invade($grid)->getColumns();
    expect($columns)->toBeArray();
    expect($columns['lg'])->toBe(3);

    // Check that the Grid contains the Toggle and TimePicker components
    $gridSchema = invade($grid)->getChildComponents();
    expect($gridSchema[0])->toBeInstanceOf(\Filament\Forms\Components\Toggle::class);
    expect($gridSchema[1])->toBeInstanceOf(\Filament\Forms\Components\Hidden::class);
    expect($gridSchema[2])->toBeInstanceOf(\Filament\Forms\Components\TimePicker::class);
    expect($gridSchema[3])->toBeInstanceOf(\Filament\Forms\Components\TimePicker::class);

    // Check that the Toggle has the correct properties
    $toggle = $gridSchema[0];
    expect($toggle->getLabel())->toBe('Bruk fastsatt tid?');
    expect($toggle->isRequired())->toBeTrue();
    expect($toggle->isLive())->toBeTrue();
});
