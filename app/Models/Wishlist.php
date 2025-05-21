<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Wishlist
 *
 * Representerer en ønskeliste som inneholder elementer brukeren vil kjøpe, samle eller holde oversikt over.
 *
 * @property int $id Primærnøkkel for ønskelisten.
 * @property string $hva Navn eller beskrivelse av ønsket item.
 * @property float $koster Pris på ønsket item.
 * @property string $url Lenke til ønsket item.
 * @property int $antall Antall av ønsket item.
 * @property string $status Status for ønsket item (f.eks. "ønsket", "kjøpt").
 * @property int $prioritet Prioritetsgrad for ønsket item.
 * @property \Illuminate\Support\Carbon|null $created_at Tidspunkt for opprettelse av ønskelisten.
 * @property \Illuminate\Support\Carbon|null $updated_at Tidspunkt for siste oppdatering.
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WishlistItem[] $wishlistItems
 * @property-read int|null $wishlist_items_count
 *
 * @mixin \Eloquent
 */
class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'hva',
        'koster',
        'url',
        'antall',
        'status',
        'prioritet',
    ];

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }
}
