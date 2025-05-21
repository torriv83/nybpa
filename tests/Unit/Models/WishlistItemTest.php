<?php

namespace Tests\Unit\Models;

use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WishlistItemTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_create_a_wishlist_item()
    {
        $wishlist = Wishlist::create([
            'hva' => 'Test Wishlist',
            'koster' => 1000.50,
            'url' => 'https://example.com/product',
            'antall' => 2,
            'status' => 'ønsket',
            'prioritet' => 1,
        ]);

        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'hva' => 'Test Item',
            'url' => 'https://example.com/item',
            'koster' => 500.25,
            'antall' => 3,
            'status' => 'ønsket',
        ]);

        $this->assertInstanceOf(WishlistItem::class, $wishlistItem);
        $this->assertEquals('Test Item', $wishlistItem->hva);
        $this->assertEquals('https://example.com/item', $wishlistItem->url);
        $this->assertEquals(500.25, $wishlistItem->koster);
        $this->assertEquals(3, $wishlistItem->antall);
        $this->assertEquals('ønsket', $wishlistItem->status);
    }

    #[Test]
    public function it_belongs_to_a_wishlist()
    {
        $wishlist = Wishlist::create([
            'hva' => 'Test Wishlist',
            'koster' => 1000.50,
            'url' => 'https://example.com/product',
            'antall' => 2,
            'status' => 'ønsket',
            'prioritet' => 1,
        ]);

        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'hva' => 'Test Item',
            'url' => 'https://example.com/item',
            'koster' => 500.25,
            'antall' => 3,
            'status' => 'ønsket',
        ]);

        $this->assertInstanceOf(Wishlist::class, $wishlistItem->wishlist);
        $this->assertEquals($wishlist->id, $wishlistItem->wishlist->id);
        $this->assertEquals('Test Wishlist', $wishlistItem->wishlist->hva);
    }

    #[Test]
    public function it_can_update_a_wishlist_item()
    {
        $wishlist = Wishlist::create([
            'hva' => 'Test Wishlist',
            'koster' => 1000.50,
            'url' => 'https://example.com/product',
            'antall' => 2,
            'status' => 'ønsket',
            'prioritet' => 1,
        ]);

        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'hva' => 'Original Item',
            'url' => 'https://example.com/original',
            'koster' => 500.25,
            'antall' => 3,
            'status' => 'ønsket',
        ]);

        // Update the wishlist item
        $wishlistItem->update([
            'hva' => 'Updated Item',
            'url' => 'https://example.com/updated',
            'koster' => 750.75,
            'antall' => 5,
            'status' => 'kjøpt',
        ]);

        // Refresh the model from the database
        $wishlistItem = $wishlistItem->fresh();

        $this->assertEquals('Updated Item', $wishlistItem->hva);
        $this->assertEquals('https://example.com/updated', $wishlistItem->url);
        $this->assertEquals(750.75, $wishlistItem->koster);
        $this->assertEquals(5, $wishlistItem->antall);
        $this->assertEquals('kjøpt', $wishlistItem->status);
    }

    #[Test]
    public function it_can_delete_a_wishlist_item()
    {
        $wishlist = Wishlist::create([
            'hva' => 'Test Wishlist',
            'koster' => 1000.50,
            'url' => 'https://example.com/product',
            'antall' => 2,
            'status' => 'ønsket',
            'prioritet' => 1,
        ]);

        $wishlistItem = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'hva' => 'Test Item',
            'url' => 'https://example.com/item',
            'koster' => 500.25,
            'antall' => 3,
            'status' => 'ønsket',
        ]);

        $wishlistItemId = $wishlistItem->id;

        // Delete the wishlist item
        $wishlistItem->delete();

        // The wishlist item should not exist in the database
        $this->assertDatabaseMissing('wishlist_items', ['id' => $wishlistItemId]);
        $this->assertNull(WishlistItem::find($wishlistItemId));
    }
}
