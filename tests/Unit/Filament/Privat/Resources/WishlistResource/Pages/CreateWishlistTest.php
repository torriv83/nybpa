<?php

namespace Tests\Unit\Filament\Privat\Resources\WishlistResource\Pages;

use App\Filament\Privat\Resources\WishlistResource;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can render page', function () {
    $this->get(WishlistResource::getUrl('create', panel: 'privat'))->assertSuccessful();
});
