<?php

namespace Tests\Unit\Filament\Privat\Resources\WishlistResource\Pages;

use App\Filament\Privat\Resources\WishlistResource\Pages\EditWishlist;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EditWishlistTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_correct_listeners()
    {
        $component = new EditWishlist;

        $reflectionClass = new \ReflectionClass($component);
        $property = $reflectionClass->getProperty('listeners');
        $property->setAccessible(true);

        $listeners = $property->getValue($component);

        $this->assertIsArray($listeners);
        $this->assertArrayHasKey('itemedited', $listeners);
        $this->assertEquals('refreshSum', $listeners['itemedited']);
    }

    #[Test]
    public function it_refreshes_sum_correctly()
    {
        // Create a wishlist
        $wishlist = Wishlist::create([
            'hva' => 'Test Wishlist',
            'koster' => 0,
            'antall' => 1,
            'status' => 'ønsket',
            'prioritet' => 1,
            'url' => 'https://example.com/product',
        ]);

        // Create wishlist items
        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'hva' => 'Item 1',
            'koster' => 100,
            'antall' => 2,
            'status' => 'ønsket',
            'url' => 'https://example.com/item1',
        ]);

        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'hva' => 'Item 2',
            'koster' => 50,
            'antall' => 3,
            'status' => 'ønsket',
            'url' => 'https://example.com/item2',
        ]);

        // Create the component
        $component = new EditWishlist;

        // Call the refreshSum method
        $component->refreshSum($wishlist->id);

        // Refresh the wishlist from the database
        $wishlist->refresh();

        // The sum should be (100 * 2) + (50 * 3) = 200 + 150 = 350
        $this->assertEquals(350, $wishlist->koster);
    }
}
