<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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

    /* @phpstan-ignore-next-line */
    use HasFactory;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles->isNotEmpty() && $this->hasVerifiedEmail() && $this->deleted_at == false;
    }

    /**
     * @var list<string>
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
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /* @phpstan-ignore-next-line */
    public function timesheet(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    /* @phpstan-ignore-next-line */
    public function setting(): HasMany
    {
        return $this->hasMany(Settings::class);
    }

    /* @phpstan-ignore-next-line */
    #[Scope]
    protected function assistenter(Builder $query): ?Builder
    {
        $rolesToCheck = ['Tilkalling', 'Fast ansatt'];
        $existingRoles = Role::whereIn('name', $rolesToCheck)->pluck('name')->toArray();

        if (! empty($existingRoles)) {
            // @phpstan-ignore-next-line
            return $query->role($existingRoles);
        }

        return $query;
    }
}
