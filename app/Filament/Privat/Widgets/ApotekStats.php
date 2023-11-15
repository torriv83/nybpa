<?php

namespace App\Filament\Privat\Widgets;

use App\Filament\Privat\Resources\UtstyrResource;
use App\Models\Resepter;
use DB;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ApotekStats extends BaseWidget
{

    protected int|string|array $columnSpan = '12';

    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $lastOrder = Carbon::parse(DB::table('filament_email_log')
            ->where('to', '=', 'svinesundparken@dittapotek.no')
            ->latest()
            ->first()->created_at)->format('d. M. Y, H:i');

        $reseptValidTo = Resepter::query()->orderBy('validTo')->first()?->validTo ?? null;

        return [
            Stat::make('Antall utstyr på lista', UtstyrResource::getEloquentQuery()->where('deleted_at', null)->count()),
            Stat::make('Siste bestilling', $lastOrder),
            Stat::make('Neste resept går ut',
                $reseptValidTo ? Carbon::parse($reseptValidTo)->diffForHumans([
                    'parts' => 2,
                    'join'  => ' og '
                ]) : 'Ingen resepter'),
        ];
    }
}
