<?php

namespace App\Filament\Resources\TestResultsResource\Pages;

use App\Filament\Resources\TestResultsResource;
use App\Models\Tests;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateTestResults extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;

    protected static string $resource = TestResultsResource::class;

    protected function getRedirectUrl(): string
    {

        return $this->getResource()::getUrl('index');
    }

    protected function getSteps(): array
    {

        return [
            Step::make('hvilken_test')
                ->label('Hvilken test?')
                ->description('Velg hvilken test du skal loggføre')
                ->schema([
                    Select::make('testsID')
                        ->options(function () {

                            return tests::all()->pluck('navn', 'id');
                        })->label('Test')->reactive(),
                    DateTimePicker::make('dato'),
                ]),

            Step::make('Resultater')
                ->description('Legg inn resultater fra testen her')
                ->schema([
                    Repeater::make('resultat')
                        ->schema(function (Get $get): array {

                            $schema = [];
                            if ($get('testsID')) {
                                $data = tests::where('id', '=', $get('testsID'))->get();
                                foreach ($data[0]['ovelser'] as $o) {
                                    if ($o['type'] == 'tid' || $o['type'] == 'kg') {
                                        $schema[] = TextInput::make($o['navn'])
                                            ->regex('/^\d{1,3}(\.\d{1,2})?$/')
                                            // ->mask(fn (TextInput\Mask $mask) => $mask->pattern('0[00].[00]'))
                                            ->required();
                                    } else {
                                        $schema[] = TextInput::make($o['navn'])
                                            ->required();
                                    }
                                }
                            }

                            return $schema;
                        }),
                    Hidden::make('testsID'),
                ]),
        ];
    }

    protected function afterCreate(): void
    {
        Cache::tags(['testresult'])->flush();
    }
}
