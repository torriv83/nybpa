<?php

namespace App\Filament\Privat\Resources\EconomyResource\Pages;

use App\Filament\Privat\Resources\EconomyResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ManageEconomies extends ManageRecords
{
    protected static string $resource = EconomyResource::class;

/*    public function getHeaderWidgetsColumns(): int|array
    {
        return 12;
    }*/

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('convertBankFile')
                ->label('Konverter Bankfil')
                ->form([
                    FileUpload::make('bankFile')
                        ->multiple(false)
                        ->label('Bank CSV-fil')
                        ->required()
                        ->disk('local')
                        ->directory('temporary'),
                ])
                ->action(function (array $data){

                    // Tilgang til den opplastede filen
                    $bankFile = $data['bankFile'];
                    $fileName = 'ynab_format.csv';
                    $pathToFile = Storage::path($bankFile);

                    // Prosesser filen og generer YNAB-kompatibel fil
                    return Excel::download(new YNABExport($pathToFile), $fileName, \Maatwebsite\Excel\Excel::CSV);
                })->after(function ($data) {
                    Storage::disk('local')->delete($data['bankFile']);
                })
                ->modalSubmitActionLabel('Konverter'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EconomyResource\Widgets\StatsOverview::class,
            EconomyResource\Widgets\AccountsOverview::class,
            EconomyResource\Widgets\YnabChart::class,
            EconomyResource\Widgets\YnabOverview::class,
        ];
    }

    public function updated($name): void
    {
        if (Str::of($name)->contains(['mountedTableAction', 'mountedTableBulkAction'])) {
            $this->dispatch('updateStatsOverview');
        }
    }
}
