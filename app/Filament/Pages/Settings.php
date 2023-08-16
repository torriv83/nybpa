<?php

namespace App\Filament\Pages;

use App\Models\Settings as Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Settings extends Page implements HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Innstillinger';

    protected static string $view = 'filament.pages.settings';

    protected static ?string $slug = 'innstillinger';

    protected static bool $shouldRegisterNavigation = false;

    public Setting $settings;

    public $user_id;
    public $weekplan_timespan;
    public $weekplan_from;
    public $weekplan_to;
    public $apotek_epost;
    public $bpa_hours_per_week;

    public function mount(): void
    {

        if (Setting::where('user_id', Auth::id())->first() !== null) {
            $this->settings = Setting::where('user_id', Auth::id())->first();
        } else {
            $this->settings = new Setting();
        }

        $this->form->fill([
            'weekplan_timespan'  => $this->settings->weekplan_timespan,
            'weekplan_from'      => $this->settings->weekplan_from,
            'weekplan_to'        => $this->settings->weekplan_to,
            'bpa_hours_per_week' => $this->settings->bpa_hours_per_week,
            'apotek_epost'       => $this->settings->apotek_epost,
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
//                ->description('Description')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Select::make('weekplan_timespan')
                                ->options([
                                    0 => 'Nei',
                                    1 => 'Ja',
                                ])
                                ->label('Bruk fastsatt tid?')->required()
                                ->reactive(),
                            Forms\Components\Hidden::make('user_id')->formatStateUsing(fn() => Auth::id()),
                            TimePicker::make('weekplan_from')->seconds(false)
                                ->format('H:i:s')
                                ->label('Vis tid fra')
                                ->hidden(fn(callable $get
                                ) => $get('weekplan_timespan') === null || $get('weekplan_timespan') === 0 || $get('weekplan_timespan') === '0'),
                            TimePicker::make('weekplan_to')->seconds(false)
                                ->format('H:i:s')
                                ->label('Vis tid til')
                                ->hidden(fn(callable $get
                                ) => $get('weekplan_timespan') === null || $get('weekplan_timespan') === 0 || $get('weekplan_timespan') === '0'),
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
                ->color('gray'),
        ];
    }

    public function submit()
    {
        Setting::updateOrCreate(['user_id' => Auth::id()], $this->form->getState());

        Notification::make()
            ->title('Innstillinger er lagret')
            ->success()
            ->send();
        //        $settings->save();

        //         SAVE THE SETTINGS HERE
    }

    public function clearCache()
    {
        return Cache::flush();
    }
}
