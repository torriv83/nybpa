<?php

namespace App\Filament\Privat\Resources\EconomyResource\Pages;

use App\Filament\Privat\Resources\EconomyResource;
use App\Filament\Privat\Resources\EconomyResource\Pages\Pipes\TransformData;
use App\Filament\Privat\Resources\EconomyResource\Pages\Pipes\TransformLines;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
                ->color('success')
                ->form([
                    FileUpload::make('bankFile')
                        ->multiple(false)
                        ->label('.CSV fil')
                        ->required()
                        ->disk('local')
                        ->directory('temporary'),
                ])
                ->action(function (array $data) {
                    return $this->processBankFile($data);
                })->after(function ($data) {
                    Storage::disk('local')->delete($data['bankFile']);
                })
                ->modalSubmitActionLabel('Konverter og last ned'),
        ];
    }

    private function processBankFile(array $data): BinaryFileResponse
    {
        // Access the uploaded file
        $bankFile = $data['bankFile'];
        $fileName = 'ynab_format.csv';
        $pathToFile = Storage::path($bankFile);

        // Process the file through the pipeline
        $processedContent = app(Pipeline::class)
            ->send($pathToFile)
            ->through([
                TransformData::class,
                TransformLines::class,
            ])
            ->thenReturn();

        // Process the file and generate YNAB-compatible file
        return Excel::download(new YNABExport($processedContent), $fileName, \Maatwebsite\Excel\Excel::CSV);
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
