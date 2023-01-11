<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Timesheet;
use App\Models\User as Users;
use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Auth\User;
use Spatie\Permission\Models\Permission;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static ?int $sort = 1;
    protected array|string|int $columnSpan = 12;

    protected function getCards(): array
    {

        $tider = Timesheet::whereBetween(
                                'fra_dato', [
                                    Carbon::parse('first day of January')
                                    ->format('Y-m-d H:i:s'), Carbon::now()
                                    ->endOfYear()
                                ]
                            )->where('unavailable', '!=', '1');

        return [
            Card::make('Antall Assistenter', Users::permission('Assistent')->count())->color('success'),
            Card::make('Timer brukt i år', sprintf('%02d',intdiv($tider->sum('totalt'), 60)) .':'. ( sprintf('%02d',$tider->sum('totalt') % 60))),
            Card::make('Timer igjen', sprintf('%02d',intdiv(21900-$tider->sum('totalt'), 60)) .':'. ( sprintf('%02d',(21900-$tider->sum('totalt')) % 60)))
                ->description('x timer i uka igjen')
        ];
    }
}