<?php

/* Brukes til å sette itensitet farger på ukeplan på de forskjellige øktene */
function getIntensityColorClass($intensity)
{
    switch ($intensity) {
        case 'crimson':
            return 'background-color: #DC2626';
        case 'darkcyan':
            return 'background-color: #008B8B';
        case 'green':
            return 'background-color: green';
        default:
            return '';
    }
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
        $colors[] = 'rgb('.mt_rand(0, 255).', '.mt_rand(0, 255).', '.mt_rand(0, 255).')';
    }

    return $colors;
}
