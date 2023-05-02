<?php

namespace App\Filament\Resources\WeekplanResource\Pages;

use App\Filament\Resources\WeekplanResource;
use App\Models\Weekplan;
use Filament\Resources\Pages\Page;

class ViewUkeplan extends Page
{
    protected static string  $resource = WeekplanResource::class;
    protected static string  $view     = 'filament.resources.weekplan-resource.pages.view-ukeplan';
    protected static ?string $title    = 'Ukeplan';

    public $record;

    public function mount($record): void
    {
        $this->record = Weekplan::find($record);
    }

    protected function getViewData(): array
    {
        return ['okter' => $this->record];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WeekplanResource\Widgets\StatsOverview::class,
        ];
    }

}
