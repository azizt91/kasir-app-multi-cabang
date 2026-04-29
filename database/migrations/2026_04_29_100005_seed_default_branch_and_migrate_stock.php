<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Seeds default branch + warehouse and migrates all existing stock data
     * from products.stock to product_warehouse pivot table.
     */
    public function up(): void
    {
        // 1. Create default branch
        $branchId = DB::table('branches')->insertGetId([
            'name' => 'Pusat',
            'address' => 'Alamat cabang pusat',
            'phone' => null,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Create default warehouse under the default branch
        $warehouseId = DB::table('warehouses')->insertGetId([
            'branch_id' => $branchId,
            'name' => 'Gudang Utama',
            'location' => 'Lokasi gudang utama',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Migrate existing product stock to product_warehouse
        $products = DB::table('products')->select('id', 'stock')->get();
        foreach ($products as $product) {
            DB::table('product_warehouse')->insert([
                'product_id' => $product->id,
                'warehouse_id' => $warehouseId,
                'stock' => $product->stock,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. Assign all existing users to default branch
        DB::table('users')->whereNull('branch_id')->update(['branch_id' => $branchId]);

        // 5. Assign all existing transactions to default branch & warehouse
        DB::table('transactions')->whereNull('branch_id')->update([
            'branch_id' => $branchId,
            'warehouse_id' => $warehouseId,
        ]);

        // 6. Assign all existing stock_movements to default warehouse
        DB::table('stock_movements')->whereNull('warehouse_id')->update([
            'warehouse_id' => $warehouseId,
        ]);

        // 7. Assign all existing purchases to default warehouse
        DB::table('purchases')->whereNull('warehouse_id')->update([
            'warehouse_id' => $warehouseId,
        ]);

        // 8. Assign all existing expenses to default branch
        DB::table('expenses')->whereNull('branch_id')->update([
            'branch_id' => $branchId,
        ]);

        // 9. Assign all existing cash_flows to default branch
        DB::table('cash_flows')->whereNull('branch_id')->update([
            'branch_id' => $branchId,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear migrated data (reverse order)
        DB::table('product_warehouse')->truncate();
        
        // Reset foreign keys to null
        DB::table('cash_flows')->update(['branch_id' => null]);
        DB::table('expenses')->update(['branch_id' => null]);
        DB::table('purchases')->update(['warehouse_id' => null]);
        DB::table('stock_movements')->update(['warehouse_id' => null]);
        DB::table('transactions')->update(['branch_id' => null, 'warehouse_id' => null]);
        DB::table('users')->update(['branch_id' => null]);

        // Delete seeded data
        DB::table('warehouses')->where('name', 'Gudang Utama')->delete();
        DB::table('branches')->where('name', 'Pusat')->delete();
    }
};
