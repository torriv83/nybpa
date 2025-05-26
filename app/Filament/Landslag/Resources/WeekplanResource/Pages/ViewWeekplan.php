<?php

namespace App\Filament\Landslag\Resources\WeekplanResource\Pages;

use App\Filament\Landslag\Resources\WeekplanResource;
use App\Filament\Landslag\Widgets\SessionsStats;
use App\Models\Weekplan;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class ViewWeekplan extends Page
{
    protected static string $resource = WeekplanResource::class;

    protected static string $view = 'filament.resources.weekplan-resource.pages.view-ukeplan';

    /**
     * @var array<int, mixed>
     */
    public array $exercises;

    public ?Weekplan $weekplan;

    public function getTitle(): string
    {
        return $this->weekplan['name'] ?? '';
    }

    public function getSubheading(): string
    {
        $weekplanUpdatedAt = $this->weekplan->updated_at;

        $latestExerciseUpdate = $this->weekplan->weekplanExercises()
            ->latest('updated_at')
            ->value('updated_at');

        $mostRecentUpdate = $latestExerciseUpdate
            ? max($weekplanUpdatedAt, $latestExerciseUpdate)
            : $weekplanUpdatedAt;

        return 'Sist oppdatert: '.Carbon::parse($mostRecentUpdate)->diffForHumans();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit')->url('./'.$this->weekplan?->id.'/edit')->label('Endre'),
        ];
    }

    public function mount(int|string $record): void
    {
        $this->weekplan = Weekplan::with('weekplanExercises')->find($record);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            SessionsStats::make(['record' => $this->weekplan]),
        ];
    }
}
