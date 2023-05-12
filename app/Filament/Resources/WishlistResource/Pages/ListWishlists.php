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

        $total     = $query->sum(fn($data) => $data->koster * $data->antall);
        $totalCost = $query->sum('koster');
        $totalItem = $query->sum('antall');

        return view('wishlist.table-footer', ['totalItem' => $totalItem, 'totalCost' => $totalCost, 'total' => $total]);
    }
}
