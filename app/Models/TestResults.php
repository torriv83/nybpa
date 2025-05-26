<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class TestResults extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'dato',
        'resultat',
        'tests_id',
    ];

    protected $casts = [
        'resultat' => 'array',
        'dato' => 'datetime',
    ];

    /* @phpstan-ignore-next-line */
    public function tests(): BelongsTo
    {
        return $this->belongsTo(Tests::class, 'tests_id');
    }

    // @phpstan-ignore-next-line
    public static function generateRandomColors(int $count): array
    {
        mt_srand(); // Seed the random number generator

        $colors = [];

        for ($i = 0; $i < $count; $i++) {
            $colors[] = 'rgb('.mt_rand(0, 255).', '.mt_rand(0, 255).', '.mt_rand(0, 255).')';
        }

        return $colors;
    }
}
