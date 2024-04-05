<?php
/**
 * Created by Tor J. Rivera.
 * Date: 03.04.2024
 * Time: 08:39
 * Company: Rivera Consulting
 */

namespace App\Filament\Privat\Resources\EconomyResource\Pages;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class YNABExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $transactions;

    use Exportable;
    public function __construct(string $pathToFile)
    {
        $this->transactions = $this->readAndTransformData($pathToFile);
    }

    private function readAndTransformData(string $pathToFile) :array
    {
        $getFileContents = file_get_contents($pathToFile);
        $fileContents = preg_replace('/^\xEF\xBB\xBF/', '', $getFileContents);
        $lines        = explode("\n", $fileContents);
        $transactions = [];

        for ($i = 1; $i < count($lines); $i++) {
            $line = $lines[$i];
            if (empty(trim($line))) {
                continue; // Hopp over tomme linjer
            }
            $data = str_getcsv($line, ';'); // Bruk semikolon som skilletegn

            if (count($data) >= 8) {
                $transactions[] = [
                    'Date'   => $data[0],
                    'Payee'  => $data[5],
                    'Memo'   => '',
                    'Amount' => str_replace(',', '.', $data[1]),
                ];
            }
        }

        return $transactions;
    }

    public function collection()
    {
        return collect($this->transactions);
    }

    public function headings(): array
    {
        // Returner kolonneoverskriftene for den endelige CSV-filen
        return ['Date', 'Payee', 'Memo', 'Amount'];
    }

    public function map($row): array
    {
        return [
            'Date'   => $row['Date'],
            'Payee'  => $row['Payee'],
            'Memo'   => '',
            'Amount' => str_replace(',', '.', $row['Amount'])
        ];
    }
}

