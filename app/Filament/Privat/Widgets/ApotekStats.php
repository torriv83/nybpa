<?php

namespace App\Filament\Privat\Widgets;

use App\Filament\Privat\Resources\ResepterResource;
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
            ->first()?->created_at)->format('d. M. Y, H:i');

        $reseptValidTo = Resepter::where('validTo', '>=', now()->toDateString())  // Sjekker at utløpsdatoen er i dag eller senere
                            ->orderBy('validTo', 'asc')  // Sorterer etter utløpsdato i stigende rekkefølge
                            ->first()?->validTo ?? null;  // Henter den første resepten i listen, som er den med nærmest utløpsdato

        return [
            Stat::make('Antall utstyr på lista', UtstyrResource::getEloquentQuery()->where('deleted_at', null)->count()),
            Stat::make('Siste bestilling', $lastOrder),
            Stat::make('Neste resept går ut',
                $reseptValidTo ? Carbon::parse($reseptValidTo)->diffForHumans([
                    'parts' => 2,
                    'join'  => ' og '
                ]) : 'Ingen resepter')
                ->description('Antall Resepter som er utgått: '. ResepterResource::getEloquentQuery()->where('validTo', '<', now())->count()),
        ];
    }
}
