<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Assuming User model is in App\Models

class Expense extends Model
{
    protected $fillable = [
        'name',
        'description',
        'amount',
        'date',
        'user_id',
        'branch_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch for the expense.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
