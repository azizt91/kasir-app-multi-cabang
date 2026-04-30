<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'is_active',
        'receipt_footer',
        'paper_size',
        'logo',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the warehouses for the branch.
     */
    public function warehouses(): HasMany
    {
        return $this->hasMany(Warehouse::class);
    }

    /**
     * Get the users assigned to the branch.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the transactions for the branch.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the expenses for the branch.
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * Get the cash flows for the branch.
     */
    public function cashFlows(): HasMany
    {
        return $this->hasMany(CashFlow::class);
    }

    /**
     * Scope to only include active branches.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the default (primary) warehouse for this branch.
     */
    public function getDefaultWarehouse()
    {
        return $this->warehouses()->where('is_active', true)->first();
    }

    /**
     * Get branch address or fallback to global setting.
     */
    public function getReceiptAddress(): string
    {
        return $this->address ?: Setting::getStoreSettings()->store_address ?? '';
    }

    /**
     * Get branch phone or fallback to global setting.
     */
    public function getReceiptPhone(): string
    {
        return $this->phone ?: Setting::getStoreSettings()->store_phone ?? '';
    }

    /**
     * Get branch receipt footer or fallback to global setting.
     */
    public function getReceiptFooter(): string
    {
        return $this->receipt_footer ?: Setting::getStoreSettings()->store_description ?? '';
    }

    /**
     * Get branch receipt logo or fallback to global setting.
     */
    public function getReceiptLogo(): ?string
    {
        return $this->logo ?: Setting::getStoreSettings()->store_logo;
    }

    /**
     * Get branch paper size.
     */
    public function getPaperSize(): string
    {
        return $this->paper_size ?: '58';
    }
}
