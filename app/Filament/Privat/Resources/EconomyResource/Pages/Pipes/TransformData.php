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
     * @param string $pathToFile The path to the file containing transactions.
     * @param \Closure $next The next middleware.
     *
     * @return mixed The result of the next middleware.
     */
    public function handle($pathToFile, Closure $next)
    {
        $fileContents = $this->readFile($pathToFile);
        $lines = $this->parseFileContent($fileContents);
        $transactions = $this->processLines($lines);
        return $next($transactions);
    }

    protected function readFile($pathToFile): false|string
    {
        return file_get_contents($pathToFile);
    }

    protected function parseFileContent($fileContents): array
    {
        return explode("\n", preg_replace('/^\xEF\xBB\xBF/', '', $fileContents));
    }

    protected function processLines($lines): array
    {
        $transactions = [];
        foreach ($lines as $i => $line) {
            if ($i == 0 || empty(trim($line))) {
                continue;
            }

            if (($data = str_getcsv($line, ';')) && count($data) >= self::MINIMUM_DATA_COUNT) {
                $transactions[] = $data;
            }
        }
        return $transactions;
    }
}
