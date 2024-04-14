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
    /**
     * Handles a .CSV file containing transactions.
     *
     * @param string $pathToFile The path to the file containing transactions.
     * @param Closure $next The next middleware.
     *
     * @return mixed The result of the next middleware.
     */
    public function handle(string $pathToFile, Closure $next): mixed
    {
        $getFileContents = file_get_contents($pathToFile);
        $lines           = explode("\n", preg_replace('/^\xEF\xBB\xBF/', '', $getFileContents));
        $transactions    = [];

        foreach ($lines as $i => $line) {
            if ($i == 0 || empty(trim($line))) {
                continue;
            }

            if (($data = str_getcsv($line, ';')) && count($data) >= 8) {
                $transactions[] = $data;
            }
        }

        return $next($transactions);
    }
}
