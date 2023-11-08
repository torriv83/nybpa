<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class TestResults extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'dato',
        'resultat',
        'testsID',
    ];

    protected $casts = [
        'resultat' => 'array',
        'dato'     => 'datetime',
    ];

    public function tests(): BelongsTo
    {
        return $this->belongsTo(Tests::class, 'testsID');
    }

    public static function generateRandomColors(int $count): array
    {
        mt_srand(); // Seed the random number generator

        $colors = [];

        for ($i = 0; $i < $count; $i++)
        {
            $colors[] = 'rgb('.mt_rand(0, 255).', '.mt_rand(0, 255).', '.mt_rand(0, 255).')';
        }

        return $colors;
    }

}
