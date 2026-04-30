<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);
    }

    protected $fillable = [
        'supplier_id',
        'warehouse_id',
        'transaction_code',
        'date',
        'total_amount',
        'note',
        'user_id',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the warehouse for the purchase.
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
