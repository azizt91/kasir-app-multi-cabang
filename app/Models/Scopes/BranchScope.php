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

        // Only apply scope if user is authenticated and is NOT admin
        if ($user && $user->role !== 'admin' && $user->branch_id) {
            $builder->where($model->getTable() . '.branch_id', $user->branch_id);
        }
    }
}
