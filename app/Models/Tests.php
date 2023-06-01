<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

/**
 * @mixin IdeHelperTests
 */
class Tests extends Model
{
    use HasFactory;
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

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResults::class, 'testsID');
    }
}
