<?php

namespace App\Filament\Privat\Resources\EconomyResource\Pages;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * @implements \Maatwebsite\Excel\Concerns\WithMapping<array<int, string|int|float>>
 */
class YNABExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @var array<int, array<int, string|int|float>>
     */
    protected array $transactions;

    use Exportable;

    /**
     * @param  array<int, array<int, string|int|float>>  $transactions
     */
    public function __construct(array $transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * @return \Illuminate\Support\Collection<int, array<int, string|int|float>>
     */
    public function collection(): Collection
    {
        return collect($this->transactions);
    }

    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return ['Date', 'Payee', 'Memo', 'Amount'];
    }

    /**
     * @param  array<int, string|int|float>  $row
     * @return array<int, string|int|float>
     */
    public function map($row): array
    {
        return $row;
    }
}
