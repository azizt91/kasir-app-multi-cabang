<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashFlow extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);
    }

    protected $fillable = [
        'date',
        'type',
        'category',
        'amount',
        'note',
        'user_id',
        'branch_id',
        'status',           // pending | approved | rejected
        'is_adjustment',    // true = generated from shift discrepancy
        'shift_id',         // links back to the shift for audit
        'rejection_reason', // reason provided by Superadmin when rejecting
    ];

    protected $casts = [
        'date'          => 'date',
        'amount'        => 'decimal:2',
        'is_adjustment' => 'boolean',
    ];

    /**
     * Get the user that recorded the cash flow.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch for the cash flow.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the shift this adjustment originated from.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'shift_id');
    }

    /**
     * Scope: Only records that actually affect the cash balance.
     * Excludes pending adjustments so they don't distort the real balance.
     * Use this scope for ALL balance calculations.
     */
    public function scopeAffectingBalance(Builder $query): Builder
    {
        return $query->where(function ($q) {
            // Normal records (not adjustments) always count
            $q->where('is_adjustment', false)
              // Approved adjustments also count
              ->orWhere(function ($q2) {
                  $q2->where('is_adjustment', true)
                     ->where('status', 'approved');
              });
        });
    }

    /**
     * Scope: Only pending discrepancy records waiting for Superadmin approval.
     */
    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('is_adjustment', true)->where('status', 'pending');
    }
}
