<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'weekplan_timespan', 'bpa_hours_per_week', 'weekplan_from', 'weekplan_to'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault(['name' => 'Tidligere ansatt']);
    }
}
