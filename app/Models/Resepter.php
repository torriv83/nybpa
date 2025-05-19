<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Resepter
 *
 * Representerer en resept eller medisinsk behandling.
 *
 * @property int $id Primærnøkkel for resepten.
 * @property string $name Navn på resepten.
 * @property string $description Beskrivelse av resepten.
 * @property string $medication Navn på medisin i resepten.
 * @property int $quantity Antall medisiner foreskrevet.
 * @property Carbon|null $validTo Gyldighetsdato for resepten.
 * @property Carbon|null $created_at Tidspunkt for opprettelse av resepten.
 * @property Carbon|null $updated_at Tidspunkt for siste oppdatering av resepten.
 *
 * @mixin \Eloquent
 */
class Resepter extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'validTo',
    ];

    protected $casts = [
        'validTo' => 'datetime',
    ];
}
