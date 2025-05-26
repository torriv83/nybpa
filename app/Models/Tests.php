<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Tests extends Model
{
    use Notifiable;
    use SoftDeletes;

    // protected $table = 'tests';

    public $timestamps = true;

    protected $fillable = [
        'navn',
        'ovelser',
    ];

    protected $casts = [
        'ovelser' => 'array',
    ];

    /* @phpstan-ignore-next-line */
    public function testResults(): HasMany
    {
        return $this->hasMany(TestResults::class, 'tests_id');
    }
}
