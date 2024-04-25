<?php

namespace App\Filament\Privat\Resources\UtstyrResource\Pages;

use App\Filament\Privat\Resources\UtstyrResource;
use App\Models\Kategori;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUtstyrs extends ListRecords
{
    protected static string $resource = UtstyrResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {

        $categories = Kategori::all();
        $tabs       = [
            'alle' => Tab::make(),
        ];

        foreach ($categories as $category) {
            $tabs[$category->kategori] = Tab::make()->modifyQueryUsing(fn(Builder $query) => $query->where('kategori_id', $category->id));
        }

        return $tabs;
    }
}
