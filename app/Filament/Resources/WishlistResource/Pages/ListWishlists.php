<?php

namespace App\Filament\Resources\WishlistResource\Pages;

use App\Filament\Resources\WishlistResource;
use App\Models\Wishlist;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\View\View;

class ListWishlists extends ListRecords
{
    protected static string $resource = WishlistResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableContentFooter(): ?View
    {
        $query = Wishlist::query()->get();

        $total = $query->sum(fn ($data) => $data->koster * $data->antall);
        $totalCost = $query->pluck('koster')->sum();
        $totalItem = $query->pluck('antall')->sum();

        return view('wishlist.table-footer', compact('totalItem', 'totalCost', 'total'));
    }
}
