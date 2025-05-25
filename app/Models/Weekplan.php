<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * Class Weekplan
 *
 * Representerer en ukeplan som kan inneholde flere øvelser knyttet til den.
 * Bruker Laravel Eloquent for å interagere med databasen.
 *
 *
 * @property int $id Primærnøkkel for ukeplanen.
 * @property string $name Navn på ukeplanen.
 * @property Carbon|null $deleted_at Angir om ukeplanen er mykt slettet.
 * @property Carbon|null $created_at Tidspunkt for opprettelse av registrering.
 * @property Carbon|null $updated_at Tidspunkt for oppdatering av registrering.
 *
 * @method static Builder|Weekplan active() Henter kun aktive ukeplaner.
 */
class Weekplan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        //
    ];

    /**
     * Relasjon til `WeekplanExercise` for å hente alle øvelsene knyttet til en ukeplan.
     */
    public function weekplanExercises(): HasMany
    {
        return $this->hasMany(WeekplanExercise::class);
    }

    /**
     * Retrieves a query scope that filters the query to include only active records.
     *
     * @param  mixed  $query  The query builder instance.
     * @return mixed The modified query builder instance.
     */
    #[Scope]
    protected function active($query): mixed
    {
        return $query->where('is_active', 1);
    }
}
