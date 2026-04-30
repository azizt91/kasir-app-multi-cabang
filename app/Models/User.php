<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property int|null $branch_id
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read bool $is_admin
 * @property-read bool $is_kasir
 * @property-read \App\Models\Branch|null $branch
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User admin()
 * @method static \Illuminate\Database\Eloquent\Builder|User kasir()
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'branch_id',
        'permissions',
        'fcm_token', // Added
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
        ];
    }

    /**
     * Check if user has specific permission
     */
    public function hasPermission($permission): bool
    {
        if ($this->role === 'admin') {
            return true;
        }
        return !empty($this->permissions) && !empty($this->permissions[$permission]);
    }

    /**
     * Get the branch that the user belongs to.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the shifts for the user.
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(CashierShift::class);
    }

    /**
     * Check if user is admin.
     *
     * @return bool
     */
    public function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is kasir.
     *
     * @return bool
     */
    public function getIsKasirAttribute(): bool
    {
        return $this->role === 'kasir';
    }

    /**
     * Determine if this user is a Superadmin.
     * Superadmin is defined as role=admin WITH branch_id=null (no branch assignment).
     * A Branch Admin has role=admin but WITH a specific branch_id.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'admin' && is_null($this->branch_id);
    }



    /**
     * Get the active warehouse for the user's branch.
     * Returns the first active warehouse of the user's branch.
     *
     * @return \App\Models\Warehouse|null
     */
    public function getActiveWarehouse(): ?Warehouse
    {
        if ($this->isSuperAdmin()) {
            if (session('admin_active_branch_id')) {
                return Warehouse::where('branch_id', session('admin_active_branch_id'))
                    ->active()
                    ->first();
            }
            // Superadmin is in Global View, return null to force manual selection in POS
            return null;
        }

        if (!$this->branch_id) {
            return Warehouse::active()->first();
        }

        return Warehouse::where('branch_id', $this->branch_id)
            ->active()
            ->first();
    }

    /**
     * Scope a query to only include admin users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAdmin($query)
    {
        return $query->where('role', 'admin');
    }

    /**
     * Scope a query to only include kasir users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeKasir($query)
    {
        return $query->where('role', 'kasir');
    }
    /**
     * Get the name of the currently active branch.
     * - Superadmin (branch_id=null): returns session branch name or 'Semua Cabang'
     * - Branch Admin & Kasir (branch_id set): always returns their own branch name
     * 
     * @return string
     */
    public function getActiveBranchName(): string
    {
        if (!$this->isSuperAdmin()) {
            // Branch Admin & Kasir: always locked to their own branch
            return $this->branch->name ?? 'N/A';
        }
        
        // Superadmin: check session filter
        $activeBranchId = session('admin_active_branch_id');
        if (!$activeBranchId) {
            return 'Semua Cabang';
        }
        
        $branch = \App\Models\Branch::find($activeBranchId);
        return $branch ? $branch->name : 'Semua Cabang';
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @return string|null
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
}