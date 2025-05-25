<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class WishlistItem
 *
 * Representerer et enkelt element i en ønskeliste.
 *
 * @property int $id Primærnøkkel for wishlist-item.
 * @property int $wishlist_id ID tilknyttet ønskelisten (foreign key).
 * @property string $hva Navn/beskrivelse av wishlist-element.
 * @property string $url URL til wishlist-elementet.
 * @property float $koster Kostnad for elementet.
 * @property int $antall Antall ønsket av elementet.
 * @property string $status Status for elementet (f.eks. "kjøpt", "ønsket").
 * @property Carbon|null $created_at Tidspunkt da elementet ble opprettet.
 * @property Carbon|null $updated_at Tidspunkt da elementet ble sist oppdatert.
 *
 * @mixin Eloquent
 */
class WishlistItem extends Model
{
    protected $fillable = [
        'wishlist_id',
        'hva',
        'koster',
        'url',
        'antall',
        'status',
    ];

    public function wishlist(): BelongsTo
    {
        return $this->belongsTo(Wishlist::class);
    }
}
