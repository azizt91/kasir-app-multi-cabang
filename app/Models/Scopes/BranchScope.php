<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * BranchScope - Automatically filters records by the authenticated user's branch.
 * 
 * Admin users are EXEMPT from this scope (they see all branches).
 * Kasir users only see records from their assigned branch.
 */
class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();
        if (!$user) return;

        $branchId = null;

        // 1. Priority: If user has a branch_id (Kasir OR Branch Admin), always lock to it.
        //    Superadmin has branch_id = null, so they skip this block.
        if (!is_null($user->branch_id)) {
            $branchId = $user->branch_id;
        }
        // 2. Superadmin only: respect the Hybrid View session filter
        elseif ($user->isSuperAdmin() && session('admin_active_branch_id')) {
            $branchId = session('admin_active_branch_id');
        }

        // 2. Apply the scope if a branch is determined
        if ($branchId) {
            if ($model instanceof \App\Models\Purchase || $model instanceof \App\Models\StockMovement) {
                // Models related to branch via warehouse
                $builder->whereHas('warehouse', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            } elseif ($model instanceof \App\Models\StockTransfer) {
                // Stock transfers involving this branch's warehouses
                $builder->where(function ($q) use ($branchId) {
                    $q->whereHas('fromWarehouse', function ($q2) use ($branchId) {
                        $q2->where('branch_id', $branchId);
                    })->orWhereHas('toWarehouse', function ($q2) use ($branchId) {
                        $q2->where('branch_id', $branchId);
                    });
                });
            } elseif ($model instanceof \App\Models\CashierShift) {
                // Shifts related to branch via user
                $builder->whereHas('user', function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId);
                });
            } else {
                // Standard models with direct branch_id
                $builder->where($model->getTable() . '.branch_id', $branchId);
            }
        }
    }
}
