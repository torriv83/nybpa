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

    /**
     * Get all of the test results for this test.
     *
     * @return HasMany<TestResults, $this>
     */
    public function testResults(): HasMany
    {
        return $this->hasMany(TestResults::class, 'tests_id');
    }
}
