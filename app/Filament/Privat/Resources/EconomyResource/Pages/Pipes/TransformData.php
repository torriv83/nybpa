<?php

/**
 * Created by Tor J. Rivera.
 * Date: 13.04.2024
 * Time: 14:00
 * Company: Rivera Consulting
 */

namespace App\Filament\Privat\Resources\EconomyResource\Pages\Pipes;

use Closure;

class TransformData
{
    const MINIMUM_DATA_COUNT = 8;

    /**
     * Handles a .CSV file containing transactions.
     *
     * @param  string  $pathToFile  The path to the file containing transactions.
     * @param  Closure  $next  The next middleware.
     * @return mixed The result of the next middleware.
     */
    public function handle(string $pathToFile, Closure $next): mixed
    {
        $fileContents = $this->readFile($pathToFile);
        $lines = $this->parseFileContent($fileContents);
        $transactions = $this->processLines($lines);

        return $next($transactions);
    }

    protected function readFile(string $pathToFile): false|string
    {
        return file_get_contents($pathToFile);
    }

    /**
     * @return array<int, string>
     */
    protected function parseFileContent(string $fileContents): array
    {
        return explode("\n", preg_replace('/^\xEF\xBB\xBF/', '', $fileContents));
    }

    /**
     * @param  array<int, string>  $lines
     * @return array<int, array<int, string>>
     */
    protected function processLines(array $lines): array
    {
        $transactions = [];
        foreach ($lines as $i => $line) {
            if ($i == 0 || empty(trim($line))) {
                continue;
            }

            $data = str_getcsv($line, ';');
            // Legg kun til rader med nok kolonner
            if (count($data) >= self::MINIMUM_DATA_COUNT) {
                $transactions[] = $data;
            }
        }

        return $transactions;
    }
}
