<?php

namespace App\Filament\Resources\WeekplanResource\Pages;

use App\Filament\Resources\WeekplanResource;
use App\Models\Weekplan;
use Carbon\Carbon;
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

        // Create a new Laravel collection from the array
        $collection = collect($this->record['data']);
        return ['okter' => $collection];
    }

    protected function getTitle(): string
    {
        // Retrieve the dynamic title from a database, configuration, or any other source
        return $this->record['name'];
    }

    protected function getSubheading(): string
    {
        $dynamicSubheading = $this->record['updated_at'];
        $formattedDate     = '';

        if ($dynamicSubheading) {
            $formattedDate = Carbon::parse($dynamicSubheading)->format('d.m.Y H:i');
        }

        return 'Sist oppdatert: ' . $formattedDate;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WeekplanResource\Widgets\StatsOverview::class,
        ];
    }

}
