<?php

namespace App\Filament\Privat\Resources\WishlistResource\Pages;

use App\Filament\Privat\Resources\WishlistResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWishlist extends CreateRecord
{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected static string $resource = WishlistResource::class;
}
