<?php

namespace App\Filament\Pages;

use App\Models\Settings as Setting;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Settings extends Page implements HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.settings';

    public Setting $settings;

    public function mount(): void
    {
//        $this->settings = Setting::where('user_id', Auth::id())->first();

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

            Section::make('Ukeplan')
//                ->description('Description')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            Select::make('weekplan_timespan')
                                ->options([
                                    0 => 'Nei',
                                    1 => 'Ja'
                                ])
                                ->label('Bruk fastsatt tid?')->required()
                                ->reactive(),
                            Forms\Components\Hidden::make('user_id')->formatStateUsing(fn() => Auth::id()),
                            TimePicker::make('weekplan_from')->withoutSeconds()
                                ->format('H:i:s')
                                ->label('Vis tid fra')
                                ->hidden(fn(callable $get
                                ) => $get('weekplan_timespan') === null || $get('weekplan_timespan') === 0 || $get('weekplan_timespan') === '0'),
                            TimePicker::make('weekplan_to')->withoutSeconds()
                                ->format('H:i:s')
                                ->label('Vis tid til')
                                ->hidden(fn(callable $get
                                ) => $get('weekplan_timespan') === null || $get('weekplan_timespan') === 0 || $get('weekplan_timespan') === '0')
                        ])
                ])
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('Lagre')->action('submit'),
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
}
