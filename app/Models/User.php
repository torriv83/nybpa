<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;
    use SoftDeletes;

    public function canAccessFilament(): bool
    {
        return $this->email == 'tor@trivera.net' && $this->hasVerifiedEmail();
    }

    /**
     * @return bool
     */
    public function canImpersonate(): bool
    {
        // For example
        return User::can('Impersonate');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'adresse',
        'postnummer',
        'poststed',
        'assistentnummer',
        'ansatt_dato',
        'password',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function setPasswordAttribute($pass)
    // {
    //     $this->attributes['password'] = Hash::make($pass);
    // }

    /**
     * @return HasMany
     */
    public function timesheet(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function setting(): HasMany
    {
        return $this->hasMany(Settings::class);
    }

    public function scopeAssistenter($query)
    {
        if (Role::where('name', 'tilkalling')->exists()) {
            return $query->role(['Fast ansatt', 'Tilkalling']);
        }

        return null;
    }
}
