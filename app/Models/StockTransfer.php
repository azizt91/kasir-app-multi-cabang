<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransfer extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\BranchScope);
    }

    protected $fillable = [
        'transfer_code',
        'product_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'quantity',
        'status',
        'user_id',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    /**
     * Get the product being transferred.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the source warehouse.
     */
    public function fromWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    /**
     * Get the destination warehouse.
     */
    public function toWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    /**
     * Get the user who initiated the transfer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate a unique transfer code.
     */
    public static function generateTransferCode(): string
    {
        $date = now()->format('Ymd');
        $prefix = "TRF{$date}";

        $lastTransfer = static::where('transfer_code', 'like', "{$prefix}%")
            ->orderBy('transfer_code', 'desc')
            ->first();

        if ($lastTransfer) {
            $lastSequence = (int) substr($lastTransfer->transfer_code, -4);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}
