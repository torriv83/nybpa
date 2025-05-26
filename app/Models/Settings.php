<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class Settings extends Model
{
    protected $fillable = ['user_id', 'weekplan_timespan', 'bpa_hours_per_week', 'weekplan_from', 'weekplan_to', 'apotek_epost'];

    protected $casts = [
        'weekplan_timespan' => 'boolean',
    ];

    /** @phpstan-ignore-next-line */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault(['name' => 'Tidligere ansatt']);
    }

    public static function getUserBpa(): ?int
    {
        $userId = Auth::id();
        $setting = self::getUserSettings($userId);

        return $setting['bpa_hours_per_week'] ?? 1;
    }

    public static function getUserApotekEpost(): ?string
    {
        $userId = Auth::id();
        $setting = self::getUserSettings($userId);

        return $setting['apotek_epost'];
    }

    /** @phpstan-ignore-next-line */
    public static function getUserSettings($userId): ?Settings
    {
        return Cache::tags(['settings'])->remember("user-settings-{$userId}", now()->addMonth(), function () use ($userId) {
            return self::where('user_id', '=', $userId)->first();
        });
    }
}
