<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property string $barcode
 * @property string $name
 * @property string|null $description
 * @property int $category_id
 * @property string $purchase_price
 * @property string $selling_price
 * @property int $stock
 * @property int $minimum_stock
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StockMovement[] $stockMovements
 * @property-read int|null $stock_movements_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TransactionItem[] $transactionItems
 * @property-read int|null $transaction_items_count
 * @property-read bool $is_low_stock
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereBarcode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereMinimumStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product wherePurchasePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereSellingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereStock($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Product lowStock()
 * @method static \Database\Factories\ProductFactory factory($count = null, $state = [])
 * 
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'product_group_id',
        'barcode',
        'name',
        'variant_name',
        'description',
        'category_id',
        'purchase_price',
        'selling_price',
        'stock',
        'minimum_stock',
        'image',
    ];

    public function productGroup()
    {
        return $this->belongsTo(ProductGroup::class);
    }


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'decimal:2',
        'minimum_stock' => 'integer',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the warehouses this product is stocked in.
     */
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'product_warehouse')
            ->withPivot('stock')
            ->withTimestamps();
    }

    /**
     * Get the stock movements for the product.
     */
    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get the transaction items for the product.
     */
    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get stock for a specific warehouse.
     *
     * @param int $warehouseId
     * @return float
     */
    public function getStockInWarehouse(int $warehouseId): float
    {
        $pivot = \Illuminate\Support\Facades\DB::table('product_warehouse')
            ->where('product_id', $this->id)
            ->where('warehouse_id', $warehouseId)
            ->first();

        return $pivot ? (float) $pivot->stock : 0;
    }

    /**
     * Get total stock across all warehouses.
     *
     * @return float
     */
    public function getTotalStock(): float
    {
        return (float) \Illuminate\Support\Facades\DB::table('product_warehouse')
            ->where('product_id', $this->id)
            ->sum('stock');
    }

    /**
     * Check if product stock is low (uses total stock across all warehouses).
     *
     * @return bool
     */
    public function getIsLowStockAttribute(): bool
    {
        return $this->getTotalStock() <= $this->minimum_stock;
    }

    /**
     * Scope a query to only include products with low stock (based on total stock).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLowStock($query)
    {
        return $query->whereHas('warehouses', function () {})
            ->whereRaw('(SELECT COALESCE(SUM(pw.stock), 0) FROM product_warehouse pw WHERE pw.product_id = products.id) <= products.minimum_stock');
    }

    /**
     * Scope: products with stock in a specific warehouse.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $warehouseId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeInWarehouse($query, int $warehouseId)
    {
        return $query->whereHas('warehouses', function ($q) use ($warehouseId) {
            $q->where('warehouses.id', $warehouseId)
              ->where('product_warehouse.stock', '>', 0);
        });
    }

    /**
     * Get the product image URL.
     *
     * @return string|null
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Check if it's already a full URL
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];
}