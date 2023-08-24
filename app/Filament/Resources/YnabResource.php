<?php
/**
 * Created by ${USER}.
 * Date: 25.08.2023
 * Time: 00.01
 * Company: Rivera Consulting
 */

namespace App\Filament\Resources;

use App\Filament\Resources\YnabResource\Pages;
use App\Models\Ynab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class YnabResource extends Resource
{
    protected static ?string $model = Ynab::class;

    protected static ?string $slug = 'ynabs';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListYnabs::route('/'),
            'create' => Pages\CreateYnab::route('/create'),
            'edit'   => Pages\EditYnab::route('/{record}/edit'),
        ];
    }
}
