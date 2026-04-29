<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── 1. Cabang & Gudang ──────────────────────────────────────────
        $branchPusat = Branch::create([
            'name' => 'Cabang Utama (Pusat)',
            'address' => 'Jl. Sudirman No. 1, Jakarta',
            'phone' => '021-12345678',
            'is_active' => true,
        ]);

        $branchBandung = Branch::create([
            'name' => 'Cabang Bandung',
            'address' => 'Jl. Asia Afrika No. 10, Bandung',
            'phone' => '022-87654321',
            'is_active' => true,
        ]);

        $whPusat = Warehouse::create([
            'branch_id' => $branchPusat->id,
            'name' => 'Gudang Pusat',
            'location' => 'Belakang Toko Utama',
            'is_active' => true,
        ]);

        $whBandung = Warehouse::create([
            'branch_id' => $branchBandung->id,
            'name' => 'Gudang Bandung',
            'location' => 'Lantai 2 Toko Bandung',
            'is_active' => true,
        ]);

        // ─── 2. Users ───────────────────────────────────────────────
        $superadmin = User::create([
            'name' => 'Superadmin',
            'email' => 'admin@minimarket.com', // Keep original email for easy login
            'password' => bcrypt('password'),
            'role' => 'admin',
            'branch_id' => null,
            'email_verified_at' => now(),
        ]);

        $adminPusat = User::create([
            'name' => 'Admin Pusat',
            'email' => 'admin.pusat@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'branch_id' => $branchPusat->id,
            'email_verified_at' => now(),
        ]);

        $kasirPusat = User::create([
            'name' => 'Kasir Pusat',
            'email' => 'kasir1@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'branch_id' => $branchPusat->id,
            'email_verified_at' => now(),
        ]);

        $kasirBandung = User::create([
            'name' => 'Kasir Bandung',
            'email' => 'kasir2@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'branch_id' => $branchBandung->id,
            'email_verified_at' => now(),
        ]);

        // ─── 3. Categories ──────────────────────────────────────────
        $catMakanan   = Category::create(['name' => 'Makanan & Minuman',       'description' => 'Produk makanan dan minuman']);
        $catElektro   = Category::create(['name' => 'Elektronik',              'description' => 'Peralatan elektronik']);
        $catRumah     = Category::create(['name' => 'Peralatan Rumah Tangga',  'description' => 'Keperluan rumah tangga']);
        $catKesehatan = Category::create(['name' => 'Kesehatan & Kecantikan',  'description' => 'Produk kesehatan dan kecantikan']);
        $catPakaian   = Category::create(['name' => 'Pakaian & Aksesoris',     'description' => 'Pakaian dan aksesoris']);

        // ─── 4. Suppliers ───────────────────────────────────────────
        Supplier::factory(5)->create();

        // ─── 5. Products (SEMUA HARGA INTEGER / BULAT) ──────────────
        $productData = [
            // Makanan & Minuman (Distribusi Kedua Gudang)
            ['cat' => $catMakanan->id, 'name' => 'Indomie Goreng',           'barcode' => '8994001000001', 'buy' => 2500,  'sell' => 3500,   'stock' => 100, 'min' => 10],
            ['cat' => $catMakanan->id, 'name' => 'Aqua 600ml',              'barcode' => '8994001000002', 'buy' => 2000,  'sell' => 3000,   'stock' => 80,  'min' => 15],
            ['cat' => $catMakanan->id, 'name' => 'Teh Botol Sosro 450ml',   'barcode' => '8994001000003', 'buy' => 3000,  'sell' => 4500,   'stock' => 60,  'min' => 10],
            ['cat' => $catMakanan->id, 'name' => 'Beras Premium 5kg',       'barcode' => '8994001000004', 'buy' => 55000, 'sell' => 68000,  'stock' => 25,  'min' => 5],
            ['cat' => $catMakanan->id, 'name' => 'Minyak Goreng Bimoli 1L', 'barcode' => '8994001000005', 'buy' => 15000, 'sell' => 19000,  'stock' => 40,  'min' => 8],
            
            // Elektronik (Hanya di Gudang Pusat)
            ['cat' => $catElektro->id, 'name' => 'Baterai ABC AA 2pcs',     'barcode' => '8994002000001', 'buy' => 5000,  'sell' => 8000,   'stock' => 60,  'min' => 10],
            ['cat' => $catElektro->id, 'name' => 'Lampu LED Philips 9W',    'barcode' => '8994002000002', 'buy' => 15000, 'sell' => 22000,  'stock' => 30,  'min' => 5],
            ['cat' => $catElektro->id, 'name' => 'Kabel USB Type-C 1m',     'barcode' => '8994002000003', 'buy' => 10000, 'sell' => 18000,  'stock' => 25,  'min' => 5],
            
            // Peralatan Rumah Tangga (Hanya di Gudang Bandung)
            ['cat' => $catRumah->id, 'name' => 'Deterjen Rinso 800g',       'barcode' => '8994003000001', 'buy' => 14000, 'sell' => 19000,  'stock' => 40,  'min' => 8],
            ['cat' => $catRumah->id, 'name' => 'Sabun Cuci Piring Sunlight','barcode' => '8994003000002', 'buy' => 8000,  'sell' => 12000,  'stock' => 45,  'min' => 10],
            ['cat' => $catRumah->id, 'name' => 'Pel Lantai Supermop',       'barcode' => '8994003000003', 'buy' => 35000, 'sell' => 55000,  'stock' => 8,   'min' => 3],
            
            // Kesehatan & Kecantikan (Distribusi Kedua Gudang)
            ['cat' => $catKesehatan->id, 'name' => 'Pasta Gigi Pepsodent',     'barcode' => '8994004000001', 'buy' => 7000,  'sell' => 11000,  'stock' => 40,  'min' => 8],
            ['cat' => $catKesehatan->id, 'name' => 'Shampoo Pantene 170ml',    'barcode' => '8994004000002', 'buy' => 18000, 'sell' => 25000,  'stock' => 30,  'min' => 5],
        ];

        $productIndex = 0;
        foreach ($productData as $p) {
            $group = ProductGroup::create([
                'name' => $p['name'],
                'category_id' => $p['cat'],
                'has_variants' => false,
            ]);

            $product = Product::create([
                'product_group_id' => $group->id,
                'category_id' => $p['cat'],
                'name' => $p['name'],
                'barcode' => $p['barcode'],
                'purchase_price' => $p['buy'],
                'selling_price' => $p['sell'],
                'stock' => 0, // Legacy stock is 0
                'minimum_stock' => $p['min'],
            ]);

            // Scenario-based Distribution
            $totalStock = $p['stock'];
            $stockPusat = 0;
            $stockBandung = 0;

            if ($productIndex < 5 || $productIndex >= 11) {
                // Keduanya (70% Pusat, 30% Bandung)
                $stockPusat = (int) ($totalStock * 0.7);
                $stockBandung = $totalStock - $stockPusat;
            } elseif ($productIndex >= 5 && $productIndex < 8) {
                // Hanya Pusat
                $stockPusat = $totalStock;
            } elseif ($productIndex >= 8 && $productIndex < 11) {
                // Hanya Bandung
                $stockBandung = $totalStock;
            }

            // Sync Pivot Table & Movements
            if ($stockPusat > 0) {
                DB::table('product_warehouse')->insert(['product_id' => $product->id, 'warehouse_id' => $whPusat->id, 'stock' => $stockPusat, 'created_at' => now(), 'updated_at' => now()]);
                StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $whPusat->id, 'type' => 'in', 'quantity' => $stockPusat, 'notes' => 'Stok awal produk (Pusat)', 'created_at' => now()->subDays(rand(30, 60))]);
            } else {
                DB::table('product_warehouse')->insert(['product_id' => $product->id, 'warehouse_id' => $whPusat->id, 'stock' => 0, 'created_at' => now(), 'updated_at' => now()]);
            }

            if ($stockBandung > 0) {
                DB::table('product_warehouse')->insert(['product_id' => $product->id, 'warehouse_id' => $whBandung->id, 'stock' => $stockBandung, 'created_at' => now(), 'updated_at' => now()]);
                StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $whBandung->id, 'type' => 'in', 'quantity' => $stockBandung, 'notes' => 'Stok awal produk (Bandung)', 'created_at' => now()->subDays(rand(30, 60))]);
            } else {
                DB::table('product_warehouse')->insert(['product_id' => $product->id, 'warehouse_id' => $whBandung->id, 'stock' => 0, 'created_at' => now(), 'updated_at' => now()]);
            }

            $productIndex++;
        }

        // ─── 6. Variant Products (Pakaian - Distribusi Keduanya) ──
        $this->seedVariantProducts($catPakaian, $whPusat, $whBandung);

        // ─── 7. Transactions (50 dummy, SEMUA BULAT) ────────────────
        $kasirUsers = [$kasirPusat, $kasirBandung];

        for ($i = 0; $i < 50; $i++) {
            $txDate = fake()->dateTimeBetween('-30 days', 'now');
            $paymentMethod = fake()->randomElement(['cash', 'utang', 'card', 'ewallet', 'transfer', 'qris']);
            $actingUser = fake()->randomElement($kasirUsers);
            $activeWarehouse = Warehouse::where('branch_id', $actingUser->branch_id)->first();

            $transaction = Transaction::create([
                'transaction_code' => 'TRX' . date('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => $actingUser->id,
                'subtotal' => 0,
                'discount' => 0,
                'tax' => 0,
                'total_amount' => 0,
                'payment_method' => $paymentMethod,
                'amount_paid' => 0,
                'change_amount' => 0,
                'status' => 'completed',
                'customer_name' => fake()->boolean(30) ? fake()->name() : 'Umum',
                'created_at' => $txDate,
            ]);

            // Find products available in this kasir's warehouse
            $availableProductIds = DB::table('product_warehouse')
                                    ->where('warehouse_id', $activeWarehouse->id)
                                    ->where('stock', '>', 0)
                                    ->pluck('product_id');

            if ($availableProductIds->isEmpty()) continue;

            $products = Product::whereIn('id', $availableProductIds)->inRandomOrder()->take(random_int(1, 3))->get();
            $subtotal = 0;

            foreach ($products as $product) {
                $whStock = DB::table('product_warehouse')->where('product_id', $product->id)->where('warehouse_id', $activeWarehouse->id)->value('stock');
                $quantity = random_int(1, min(3, $whStock));
                $price = (int) $product->selling_price;
                $itemSubtotal = $price * $quantity;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                    'created_at' => $txDate,
                ]);

                // Reduce stock
                DB::table('product_warehouse')
                    ->where('product_id', $product->id)
                    ->where('warehouse_id', $activeWarehouse->id)
                    ->decrement('stock', $quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'warehouse_id' => $activeWarehouse->id,
                    'type' => 'out',
                    'quantity' => $quantity,
                    'reference_type' => 'App\Models\Transaction',
                    'reference_id' => $transaction->id,
                    'notes' => "Penjualan - {$transaction->transaction_code}",
                    'created_at' => $txDate,
                ]);

                $subtotal += $itemSubtotal;
            }

            // Discount
            $discount = fake()->boolean(20) ? round(rand(1000, 5000) / 500) * 500 : 0;
            $totalAmount = max(0, $subtotal - $discount);

            // Amount paid
            if ($paymentMethod === 'utang') {
                $amountPaid = 0;
                $changeAmount = 0;
            } else {
                $amountPaid = (int) ceil($totalAmount / 1000) * 1000;
                $changeAmount = $amountPaid - $totalAmount;
            }

            $transaction->update([
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total_amount' => $totalAmount,
                'amount_paid' => $amountPaid,
                'change_amount' => $changeAmount,
            ]);
        }

        // ─── 8. Other Seeders ───────────────────────────────────────
        $this->call([
            ExpenseSeeder::class,
            PurchaseSeeder::class,
            CashFlowSeeder::class,
            SettingSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅ Database seeded successfully with Multi-Branch Scenario!');
        $this->command->info('──────────────────────────────────');
        $this->command->info('Superadmin : admin@minimarket.com / password');
        $this->command->info('Kasir Pusat: kasir1@minimarket.com / password');
        $this->command->info('Kasir Bandung: kasir2@minimarket.com / password');
        $this->command->info('──────────────────────────────────');
    }

    private function seedVariantProducts(Category $fashionCategory, Warehouse $whPusat, Warehouse $whBandung): void
    {
        $tshirtGroup = ProductGroup::create([
            'name' => 'Kaos Polos Premium',
            'category_id' => $fashionCategory->id,
            'description' => 'Kaos polos bahan cotton combed 30s',
            'has_variants' => true,
        ]);

        $colors = ['Hitam', 'Putih', 'Navy'];
        $sizes = ['M', 'L', 'XL'];
        $i = 1;

        foreach ($colors as $color) {
            foreach ($sizes as $size) {
                $product = Product::create([
                    'product_group_id' => $tshirtGroup->id,
                    'name' => "Kaos Polos Premium - $color ($size)",
                    'variant_name' => "$color - $size",
                    'category_id' => $fashionCategory->id,
                    'barcode' => 'TS-' . strtoupper(substr($color, 0, 3)) . "-$size-" . str_pad($i++, 3, '0', STR_PAD_LEFT),
                    'purchase_price' => 45000,
                    'selling_price' => 85000,
                    'stock' => 0,
                    'minimum_stock' => 3,
                ]);
                
                $totalStock = random_int(10, 20);
                $stockPusat = (int) ($totalStock * 0.7);
                $stockBandung = $totalStock - $stockPusat;

                DB::table('product_warehouse')->insert([
                    ['product_id' => $product->id, 'warehouse_id' => $whPusat->id, 'stock' => $stockPusat, 'created_at' => now(), 'updated_at' => now()],
                    ['product_id' => $product->id, 'warehouse_id' => $whBandung->id, 'stock' => $stockBandung, 'created_at' => now(), 'updated_at' => now()],
                ]);

                if($stockPusat > 0) StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $whPusat->id, 'type' => 'in', 'quantity' => $stockPusat, 'notes' => 'Stok awal varian (Pusat)']);
                if($stockBandung > 0) StockMovement::create(['product_id' => $product->id, 'warehouse_id' => $whBandung->id, 'type' => 'in', 'quantity' => $stockBandung, 'notes' => 'Stok awal varian (Bandung)']);
            }
        }
    }
}
