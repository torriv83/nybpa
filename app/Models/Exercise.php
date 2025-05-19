<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Exercise
 *
 * Representerer en treningsøvelse.
 *
 * @property int $id Primærnøkkel for øvelsen.
 * @property string $name Navn på øvelsen.
 * @property Carbon|null $deleted_at Tidspunktet for når øvelsen ble "mykt slettet".
 * @property Carbon|null $created_at Tidspunktet for når øvelsen ble opprettet.
 * @property Carbon|null $updated_at Tidspunktet for når øvelsen sist ble oppdatert.
 */
class Exercise extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];
}
