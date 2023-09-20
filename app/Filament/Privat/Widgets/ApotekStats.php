<?php

namespace App\Filament\Privat\Widgets;

use App\Filament\Privat\Resources\UtstyrResource;
use App\Models\Resepter;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class ApotekStats extends BaseWidget
{
    protected function getStats(): array
    {

        $lastOrder = Carbon::parse(\DB::table('filament_email_log')
            ->where('to', '=','svinesundparken@dittapotek.no')
            ->latest()
            ->first()->created_at)->format('d.m.Y H:i');

        $reseptValidTo = Resepter::query()->orderBy('validTo')->first()?->validTo ?? null;

        return [
            Stat::make('Antall utstyr på lista', UtstyrResource::getEloquentQuery()->where('deleted_at', null)->count()),
            Stat::make('Siste bestilling', $lastOrder),
            Stat::make('Neste resept går ut', $reseptValidTo ? Carbon::parse($reseptValidTo)->diffForHumans() : 'Ingen resepter'),
        ];
    }
}
