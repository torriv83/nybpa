<?php

namespace App\Filament\Privat\Resources\WishlistResource\Pages;

use App\Filament\Privat\Resources\WishlistResource;
use App\Models\Wishlist;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWishlist extends EditRecord
{
    protected static string $resource = WishlistResource::class;

    /**
     * @var array<string, string>
     */
    protected $listeners = ['itemedited' => 'refreshSum'];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function refreshSum(int $id): void
    {

        $record = Wishlist::find($id);

        $sumItems = $record->wishlistItems->sum(function ($item): float {
            return $item->koster * $item->antall;
        });

        $record->update(['koster' => $sumItems]);

    }
}
