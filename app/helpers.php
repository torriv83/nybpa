<?php

/* Brukes til å sette itensitet farger på ukeplan på de forskjellige øktene */
function getIntensityColorClass($intensity)
{
    return match ($intensity) {
        'crimson' => 'background-color: #DC2626',
        'darkcyan' => 'background-color: #008B8B',
        'green' => 'background-color: green',
        default => '',
    };
}

/* Brukes til å sette fra-til på ukeplan kalender*/
function formatTime($from, $to)
{
    return "{$from} - {$to}";
}

/* Blir brukt for å lage random farger på charts */
function generateRandomColors(int $count): array
{
    mt_srand(7890); // Set the seed value

    $colors = [];

    for ($i = 0; $i < $count; $i++) {
        $colors[] = 'rgb(' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ', ' . mt_rand(0, 255) . ')';
    }

    return $colors;
}
