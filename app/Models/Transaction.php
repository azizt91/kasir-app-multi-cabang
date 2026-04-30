<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property string $transaction_code
 * @property int $user_id
 * @property int|null $branch_id
 * @property int|null $warehouse_id
 * @property string $subtotal
 * @property string $discount
 * @property string $tax
 * @property string $total_amount
 * @property string $payment_method
 * @property string $amount_paid
 * @property string $change_amount
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Branch|null $branch
 * @property-read \App\Models\Warehouse|null $warehouse
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TransactionItem[] $items
 * @property-read int|null $items_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereAmountPaid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereChangeAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereSubtotal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereTransactionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Transaction whereUserId($value)
 * @method static \Database\Factories\TransactionFactory factory($count = null, $state = [])
 * 
 */
class Transaction extends Model
{
    use HasFactory, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'transaction_code',
        'user_id',
        'shift_id',
        'branch_id',
        'warehouse_id',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'payment_method',
        'amount_paid',
        'change_amount',
        'status',
        'customer_name',
        'note',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_amount' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     * Apply BranchScope so kasir can only see their branch's transactions.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch for the transaction.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the warehouse for the transaction.
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get the shift that owns the transaction.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class);
    }

    /**
     * Get the transaction items for the transaction.
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Generate a unique transaction code.
     *
     * @return string
     */
    public static function generateTransactionCode(): string
    {
        $date = now()->format('Ymd');
        $prefix = "TRX{$date}";
        
        // Find the last transaction code for today (without scope)
        $lastTransaction = static::withoutGlobalScopes()
            ->where('transaction_code', 'like', "{$prefix}%")
            ->orderBy('transaction_code', 'desc')
            ->first();
        
        if ($lastTransaction) {
            // Extract the sequence number from the last transaction code
            $lastSequence = (int) substr($lastTransaction->transaction_code, -4);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }
        
        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }
}