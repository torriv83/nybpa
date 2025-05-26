<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

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
 * @property Carbon|null $created_at Tidspunkt for opprettelse av ønskelisten.
 * @property Carbon|null $updated_at Tidspunkt for siste oppdatering.
 * @property Collection|WishlistItem[] $wishlistItems
 * @property int|null $wishlist_items_count
 *
 * @mixin Eloquent
 */
class Wishlist extends Model
{
    // @phpstan-ignore-next-line
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'hva',
        'koster',
        'url',
        'antall',
        'status',
        'prioritet',
    ];

    // @phpstan-ignore-next-line
    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public const STATUS_OPTIONS = [
        'Begynt å spare' => 'Begynt å spare',
        'Spart' => 'Spart',
        'Kjøpt' => 'Kjøpt',
        'Venter' => 'Venter',
    ];
}
