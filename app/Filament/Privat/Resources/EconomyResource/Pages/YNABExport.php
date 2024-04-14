<?php

namespace App\Filament\Privat\Resources\EconomyResource\Pages;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class YNABExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $transactions;

    use Exportable;

    public function __construct(array $transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection(): Collection
    {
        return collect($this->transactions);
    }

    public function headings(): array
    {
        return ['Date', 'Payee', 'Memo', 'Amount'];
    }

    public function map($row): array
    {
        return $row;
    }
}

