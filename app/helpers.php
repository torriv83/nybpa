<?php

/* Brukes til å sette itensitet farger på ukeplan på de forskjellige øktene */

function getIntensityColorClass($intensity): string
{
    return match ($intensity) {
        'crimson' => 'bg-red-600',
        'darkcyan' => 'bg-cyan-600',
        'green' => 'bg-green-600',
        default => '',
    };
}

/* Brukes til å sette fra-til på ukeplan kalender*/
function formatTime($from, $to): string
{
    return "{$from} - {$to}";
}

/* Blir brukt for å lage random farger på charts */
function generateRandomColors(int $count): array
{
    mt_srand(); // Set the seed value

    $colors = [];

    for ($i = 0; $i < $count; $i++) {
        $colors[] = 'rgb('.mt_rand(0, 255).', '.mt_rand(0, 255).', '.mt_rand(0, 255).')';
    }

    return $colors;
}
