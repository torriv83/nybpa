<?php

namespace App\Filament\Admin\Resources\TimesheetResource\Pages;

use App\Filament\Admin\Resources\TimesheetResource;
use App\Filament\Admin\Resources\TimesheetResource\Widgets\HoursUsedEachMonth;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Ny oppføring'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            HoursUsedEachMonth::class,
        ];
    }

    public function getTabs(): array
    {

        $years = Cache::tags(['timesheet'])->remember('timesheets-years', 60 * 24, function () {
            return DB::table('timesheets')
                ->selectRaw('YEAR(fra_dato) as year')
                ->groupBy(DB::raw('YEAR(fra_dato)'))
                ->pluck('year')
                ->reverse();
        });

        $tabs = [
            'alle' => Tab::make(),
        ];

        foreach ($years as $year) {
            $tabs[$year] = Tab::make()->modifyQueryUsing(function (Builder $query) use ($year) {
                return $query->whereYear('fra_dato', '=', $year);
            });
        }

        return $tabs;
    }
}
