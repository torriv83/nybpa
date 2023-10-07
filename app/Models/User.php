<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles->isNotEmpty() && $this->hasVerifiedEmail() && $this->deleted_at == false;
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


    public function timesheet(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function setting(): HasMany
    {
        return $this->hasMany(Settings::class);
    }

    /**
     * @param $query
     * @return null
     * @method assistenter()
     */
    public function scopeAssistenter($query)
    {
        $rolesToCheck = ['Tilkalling', 'Fast ansatt'];
        $existingRoles = Role::whereIn('name', $rolesToCheck)->pluck('name')->toArray();

        if (!empty($existingRoles)) {
            return $query->role($existingRoles);
        }

        return null;
    }


}
