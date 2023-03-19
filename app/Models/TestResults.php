<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TestResults extends Model
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable   = [
        'dato',
        'resultat',
        'testsID'
    ];

    protected $casts = [
        'resultat' => 'array',
        'dato' => 'datetime',
    ];

    public function tests()
    {
        return $this->belongsTo(Tests::class, 'testsID');
    }
}
