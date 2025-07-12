<?php

namespace App\Filament\Admin\Pages;

use App\Models\Settings as Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * @property Form $form
 */
class Settings extends Page implements HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Innstillinger';

    protected static string $view = 'filament.pages.settings';

    protected static ?string $slug = 'innstillinger';

    protected static bool $shouldRegisterNavigation = false;

    public Setting $settings;

    public ?int $user_id;

    public ?bool $weekplan_timespan;

    public ?string $weekplan_from;

    public ?string $weekplan_to;

    public ?string $apotek_epost;

    public ?int $bpa_hours_per_week;

    public function mount(): void
    {

        $this->settings = Setting::getUserSettings(Auth::id()) ?? new Setting;

        $this->form->fill([
            'weekplan_timespan' => $this->settings->weekplan_timespan,
            'weekplan_from' => $this->settings->weekplan_from,
            'weekplan_to' => $this->settings->weekplan_to,
            'bpa_hours_per_week' => $this->settings->bpa_hours_per_week,
            'apotek_epost' => $this->settings->apotek_epost,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Uloba')
                ->description('Timer innvilget')
                ->schema([
                    TextInput::make('bpa_hours_per_week')->label('Antall timer i uka')->required(),
                ]),

            Section::make('Apotek')
                ->description('E-post adresse til apoteket')
                ->schema([
                    TextInput::make('apotek_epost')->label('E-post adresse')->required(),
                ]),

            Section::make('Ukeplan')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Forms\Components\Toggle::make('weekplan_timespan')
                                ->label('Bruk fastsatt tid?')
                                ->inline(false)
                                ->onColor('success')
                                ->offColor('danger')
                                ->onIcon('heroicon-m-check')
                                ->offIcon('heroicon-m-x-mark')
                                ->required()
                                ->live(),
                            Forms\Components\Hidden::make('user_id')
                                ->formatStateUsing(fn () => Auth::id()),
                            TimePicker::make('weekplan_from')->seconds(false)
                                ->format('H:i:s')
                                ->label('Vis tid fra')
                                ->hidden(
                                    fn (Get $get): bool => ! $get('weekplan_timespan')
                                ),
                            TimePicker::make('weekplan_to')->seconds(false)
                                ->format('H:i:s')
                                ->label('Vis tid til')
                                ->hidden(fn (Get $get): bool => ! $get('weekplan_timespan')),
                        ]),
                ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('Lagre')->action('submit'),
            Action::make('Cache')->action('clearCache')
                ->label('Tøm Cache')
                ->color('danger'),
        ];
    }

    public function submit(): void
    {
        Setting::updateOrCreate(['user_id' => Auth::id()], $this->form->getState());

        Notification::make()
            ->title('Innstillinger er lagret')
            ->success()
            ->send();

        Cache::tags(['settings'])->forget('user-settings-'.Auth::id());

    }

    public function clearCache(): bool
    {
        return Cache::flush();
    }
}
