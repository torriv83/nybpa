<?php

namespace App\Filament\Resources\TestResultsResource\Pages;

use Closure;
use App\Models\Tests;
use App\Models\TestResults;
use Filament\Pages\Actions;
use Illuminate\Support\Str;
use Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\TestResultsResource;
use NunoMaduro\Collision\Adapters\Phpunit\TestResult;

class CreateTestResults extends CreateRecord
{
    use CreateRecord\Concerns\HasWizard;
    protected static string $resource = TestResultsResource::class;

    protected function getSteps(): array
    {
        return [
            Step::make('hvilken_test')
                ->label('Hvilken test?')
                ->description('Velg hvilken test du skal loggføre')
                ->schema([
                    \Filament\Forms\Components\Select::make('testsID')
                        ->options(function () {
                            return tests::all()->pluck('navn', 'id');
                        })->label('Test')->reactive(),
                    \Filament\Forms\Components\DateTimePicker::make('dato'),
                ]),

            Step::make('Resultater')
                ->description('Legg inn resultater fra testen her')
                ->schema([
                    Repeater::make('resultat')
                        ->schema(function (Closure $get): array {
                            $schema = [];
                            if ($get('testsID')) {
                                $data = tests::where('id', '=', $get('testsID'))->get();
                                foreach ($data[0]['ovelser'] as $o) {
                                    if ($o['type'] == 'tid' || $o['type'] == 'kg') {
                                        $schema[] = TextInput::make($o['navn'])
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
                    Hidden::make('testsID')
                ]),
        ];
    }
}
