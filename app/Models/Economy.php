<?php

namespace App\Models;

use Cache;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\UserUnavailable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\DatabaseNotification;


/**
 * @method thisYear()
 */
class Economy extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'economy';

    public $timestamps = true;

    protected $fillable   = [
        'before_tax',
        'after_tax',
        'tax_table',
        'grunnstonad'
    ];
}
