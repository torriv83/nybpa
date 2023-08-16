<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Economy extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'economy';

    public $timestamps = true;

    protected $fillable = [
        'before_tax',
        'after_tax',
        'tax_table',
        'grunnstonad',
    ];
}
