<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperWishlist
 */
class Wishlist extends Model
{
    use HasFactory;

    protected $fillable   = [
        'hva',
        'koster',
        'url',
        'antall',
        'status',
        'prioritet',
    ];
}
