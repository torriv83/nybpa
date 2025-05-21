<?php

namespace Tests\Unit\Models;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_wishlist()
    {
        $wishlist = Wishlist::create([
            'hva' => 'Test Wishlist',
            'koster' => 1000.50,
            'url' => 'https://example.com/product',
            'antall' => 2,
            'status' => 'ønsket',
            'prioritet' => 1,
        ]);

        $this->assertInstanceOf(Wishlist::class, $wishlist);
        $this->assertEquals('Test Wishlist', $wishlist->hva);
        $this->assertEquals(1000.50, $wishlist->koster);
        $this->assertEquals('https://example.com/product', $wishlist->url);
        $this->assertEquals(2, $wishlist->antall);
        $this->assertEquals('ønsket', $wishlist->status);
        $this->assertEquals(1, $wishlist->prioritet);
    }

    #[Test]
    public function it_has_many_wishlist_items()
    {
        $wishlist = Wishlist::create([
            'hva' => 'Test Wishlist',
            'koster' => 1000.50,
            'url' => 'https://example.com/product',
            'antall' => 2,
            'status' => 'ønsket',
            'prioritet' => 1,
        ]);

        // Create some wishlist items for the wishlist
        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'hva' => 'Item 1',
            'url' => 'https://example.com/item1',
            'koster' => 500.25,
            'antall' => 1,
            'status' => 'ønsket',
        ]);

        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'hva' => 'Item 2',
            'url' => 'https://example.com/item2',
            'koster' => 750.75,
            'antall' => 3,
            'status' => 'kjøpt',
        ]);

        $this->assertInstanceOf(Collection::class, $wishlist->wishlistItems);
        $this->assertCount(2, $wishlist->wishlistItems);
        $this->assertInstanceOf(WishlistItem::class, $wishlist->wishlistItems->first());
    }

    #[Test]
    public function it_can_update_a_wishlist()
    {
        $wishlist = Wishlist::create([
            'hva' => 'Original Name',
            'koster' => 1000.50,
            'url' => 'https://example.com/product',
            'antall' => 2,
            'status' => 'ønsket',
            'prioritet' => 1,
        ]);

        // Update the wishlist
        $wishlist->update([
            'hva' => 'Updated Name',
            'koster' => 1500.75,
            'antall' => 3,
            'status' => 'kjøpt',
            'prioritet' => 2,
        ]);

        // Refresh the model from the database
        $wishlist = $wishlist->fresh();

        $this->assertEquals('Updated Name', $wishlist->hva);
        $this->assertEquals(1500.75, $wishlist->koster);
        $this->assertEquals(3, $wishlist->antall);
        $this->assertEquals('kjøpt', $wishlist->status);
        $this->assertEquals(2, $wishlist->prioritet);
        // URL should remain unchanged
        $this->assertEquals('https://example.com/product', $wishlist->url);
    }

    #[Test]
    public function it_can_delete_a_wishlist()
    {
        $wishlist = Wishlist::create([
            'hva' => 'Test Wishlist',
            'koster' => 1000.50,
            'url' => 'https://example.com/product',
            'antall' => 2,
            'status' => 'ønsket',
            'prioritet' => 1,
        ]);

        $wishlistId = $wishlist->id;

        // Delete the wishlist
        $wishlist->delete();

        // The wishlist should not exist in the database
        $this->assertDatabaseMissing('wishlists', ['id' => $wishlistId]);
        $this->assertNull(Wishlist::find($wishlistId));
    }
}
