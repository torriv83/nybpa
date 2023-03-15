<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tests extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    // protected $table = 'tests';

    public $timestamps = true;

    protected $fillable   = [
        'navn',
        'ovelser',
    ];

    protected $casts = [
        'ovelser' => 'array',
    ];

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }
}
