<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'password',
        'role', 
        'modules',
        'phone',
        'address',
        'city',
        'state',
        'zipCode',
        'additionalNotes',
        'referralSource',
        'status',
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
        'modules' => 'array',
    ];

    /**
     * Check whether user has access to a given module.
     * If modules = null → full access (admin).
     */
    public function hasModule(string $module): bool
    {
        if (empty($this->modules)) {
            return true; // full access for admins
        }

        $decoded = json_decode($this->modules, true);

        // Handle double-encoded strings
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return in_array($module, $decoded ?? []);
    }

    public function projects()
    {
        return $this->hasMany(Project::class, 'user_id');
    }

    /**
     * Return allowed module slugs for the user as array.
     * If null, treat as empty array or you can decide default behavior.
     */
    public function getModulesAttribute($value)
    {
        // Let Laravel casts handle it; this method is optional if you use $casts.
        return $this->attributes['modules'] ?? [];
    }

    /**
     * Convenience: add module to user and save.
     */
    public function grantModule(string $module): self
    {
        $modules = $this->modules ?? [];
        if (! in_array($module, $modules)) {
            $modules[] = $module;
            $this->modules = array_values($modules);
            $this->save();
        }
        return $this;
    }

    /**
     * Convenience: remove module and save.
     */
    public function revokeModule(string $module): self
    {
        $modules = $this->modules ?? [];
        $modules = array_values(array_diff($modules, [$module]));
        $this->modules = $modules;
        $this->save();
        return $this;
    }

    // scope for customer users
    public function scopeCustomers($q)
    {
        return $q->where('role', 'customer');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
