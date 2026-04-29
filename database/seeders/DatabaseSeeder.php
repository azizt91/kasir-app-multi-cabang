<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ─── Users ───────────────────────────────────────────────
        $admin = User::create([
            'name' => 'Admin Minimarket',
            'email' => 'admin@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $kasir1 = User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir1@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'email_verified_at' => now(),
        ]);

        $kasir2 = User::create([
            'name' => 'Kasir 2',
            'email' => 'kasir2@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'email_verified_at' => now(),
        ]);

        // ─── Categories ──────────────────────────────────────────
        $catMakanan   = Category::create(['name' => 'Makanan & Minuman',       'description' => 'Produk makanan dan minuman']);
        $catElektro   = Category::create(['name' => 'Elektronik',              'description' => 'Peralatan elektronik']);
        $catRumah     = Category::create(['name' => 'Peralatan Rumah Tangga',  'description' => 'Keperluan rumah tangga']);
        $catKesehatan = Category::create(['name' => 'Kesehatan & Kecantikan',  'description' => 'Produk kesehatan dan kecantikan']);
        $catPakaian   = Category::create(['name' => 'Pakaian & Aksesoris',     'description' => 'Pakaian dan aksesoris']);

        // ─── Suppliers ───────────────────────────────────────────
        Supplier::factory(5)->create();

        // ─── Products (SEMUA HARGA INTEGER / BULAT) ──────────────
        $productData = [
            // Makanan & Minuman
            ['cat' => $catMakanan->id, 'name' => 'Indomie Goreng',           'barcode' => '8994001000001', 'buy' => 2500,  'sell' => 3500,   'stock' => 100, 'min' => 10],
            ['cat' => $catMakanan->id, 'name' => 'Aqua 600ml',              'barcode' => '8994001000002', 'buy' => 2000,  'sell' => 3000,   'stock' => 80,  'min' => 15],
            ['cat' => $catMakanan->id, 'name' => 'Teh Botol Sosro 450ml',   'barcode' => '8994001000003', 'buy' => 3000,  'sell' => 4500,   'stock' => 60,  'min' => 10],
            ['cat' => $catMakanan->id, 'name' => 'Beras Premium 5kg',       'barcode' => '8994001000004', 'buy' => 55000, 'sell' => 68000,  'stock' => 25,  'min' => 5],
            ['cat' => $catMakanan->id, 'name' => 'Minyak Goreng Bimoli 1L', 'barcode' => '8994001000005', 'buy' => 15000, 'sell' => 19000,  'stock' => 40,  'min' => 8],
            ['cat' => $catMakanan->id, 'name' => 'Gula Pasir 1kg',          'barcode' => '8994001000006', 'buy' => 12000, 'sell' => 15000,  'stock' => 35,  'min' => 5],
            ['cat' => $catMakanan->id, 'name' => 'Kopi ABC Susu 10pcs',     'barcode' => '8994001000007', 'buy' => 8000,  'sell' => 12000,  'stock' => 50,  'min' => 10],
            ['cat' => $catMakanan->id, 'name' => 'Susu UHT Frisian Flag',   'barcode' => '8994001000008', 'buy' => 5000,  'sell' => 7000,   'stock' => 45,  'min' => 10],
            ['cat' => $catMakanan->id, 'name' => 'Roti Tawar Sari Roti',    'barcode' => '8994001000009', 'buy' => 10000, 'sell' => 14000,  'stock' => 20,  'min' => 5],
            ['cat' => $catMakanan->id, 'name' => 'Biscuit Oreo 137g',       'barcode' => '8994001000010', 'buy' => 7000,  'sell' => 10000,  'stock' => 55,  'min' => 10],

            // Elektronik
            ['cat' => $catElektro->id, 'name' => 'Baterai ABC AA 2pcs',     'barcode' => '8994002000001', 'buy' => 5000,  'sell' => 8000,   'stock' => 60,  'min' => 10],
            ['cat' => $catElektro->id, 'name' => 'Lampu LED Philips 9W',    'barcode' => '8994002000002', 'buy' => 15000, 'sell' => 22000,  'stock' => 30,  'min' => 5],
            ['cat' => $catElektro->id, 'name' => 'Kabel USB Type-C 1m',     'barcode' => '8994002000003', 'buy' => 10000, 'sell' => 18000,  'stock' => 25,  'min' => 5],
            ['cat' => $catElektro->id, 'name' => 'Earphone Bass JBL',       'barcode' => '8994002000004', 'buy' => 25000, 'sell' => 45000,  'stock' => 15,  'min' => 3],
            ['cat' => $catElektro->id, 'name' => 'Power Bank 10000mAh',     'barcode' => '8994002000005', 'buy' => 60000, 'sell' => 95000,  'stock' => 10,  'min' => 3],

            // Peralatan Rumah Tangga
            ['cat' => $catRumah->id, 'name' => 'Deterjen Rinso 800g',       'barcode' => '8994003000001', 'buy' => 14000, 'sell' => 19000,  'stock' => 40,  'min' => 8],
            ['cat' => $catRumah->id, 'name' => 'Sabun Cuci Piring Sunlight','barcode' => '8994003000002', 'buy' => 8000,  'sell' => 12000,  'stock' => 45,  'min' => 10],
            ['cat' => $catRumah->id, 'name' => 'Pel Lantai Supermop',       'barcode' => '8994003000003', 'buy' => 35000, 'sell' => 55000,  'stock' => 8,   'min' => 3],
            ['cat' => $catRumah->id, 'name' => 'Tissue Paseo 250 sheets',   'barcode' => '8994003000004', 'buy' => 10000, 'sell' => 15000,  'stock' => 30,  'min' => 5],
            ['cat' => $catRumah->id, 'name' => 'Sapu Ijuk',                 'barcode' => '8994003000005', 'buy' => 12000, 'sell' => 20000,  'stock' => 12,  'min' => 3],

            // Kesehatan & Kecantikan
            ['cat' => $catKesehatan->id, 'name' => 'Pasta Gigi Pepsodent',     'barcode' => '8994004000001', 'buy' => 7000,  'sell' => 11000,  'stock' => 40,  'min' => 8],
            ['cat' => $catKesehatan->id, 'name' => 'Shampoo Pantene 170ml',    'barcode' => '8994004000002', 'buy' => 18000, 'sell' => 25000,  'stock' => 30,  'min' => 5],
            ['cat' => $catKesehatan->id, 'name' => 'Sabun Mandi Lifebuoy 4pcs','barcode' => '8994004000003', 'buy' => 10000, 'sell' => 15000,  'stock' => 35,  'min' => 5],
            ['cat' => $catKesehatan->id, 'name' => 'Hansaplast 10 lembar',     'barcode' => '8994004000004', 'buy' => 5000,  'sell' => 9000,   'stock' => 25,  'min' => 5],
            ['cat' => $catKesehatan->id, 'name' => 'Minyak Kayu Putih 60ml',   'barcode' => '8994004000005', 'buy' => 12000, 'sell' => 18000,  'stock' => 20,  'min' => 5],

            // Pakaian & Aksesoris (non-variant)
            ['cat' => $catPakaian->id, 'name' => 'Topi Baseball Polos',       'barcode' => '8994005000001', 'buy' => 20000, 'sell' => 35000,  'stock' => 15,  'min' => 3],
            ['cat' => $catPakaian->id, 'name' => 'Kaos Kaki Pendek 3 Pasang', 'barcode' => '8994005000002', 'buy' => 10000, 'sell' => 18000,  'stock' => 30,  'min' => 5],
        ];

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
                'stock' => $p['stock'],
                'minimum_stock' => $p['min'],
            ]);

            // Initial stock movement
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $p['stock'],
                'notes' => 'Stok awal produk',
                'created_at' => now()->subDays(rand(30, 60)),
            ]);
        }

        // ─── Variant Products (Pakaian) ──────────────────────────
        $this->seedVariantProducts($catPakaian);

        // ─── Transactions (50 dummy, SEMUA BULAT) ────────────────
        $users = User::whereIn('role', ['admin', 'kasir'])->get();

        for ($i = 0; $i < 50; $i++) {
            $txDate = fake()->dateTimeBetween('-30 days', 'now');
            $paymentMethod = fake()->randomElement(['cash', 'utang', 'card', 'ewallet', 'transfer', 'qris']);

            $transaction = Transaction::create([
                'transaction_code' => 'TRX' . date('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'user_id' => $users->random()->id,
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

            // Add 1-4 items
            $products = Product::where('stock', '>', 0)->inRandomOrder()->take(random_int(1, 4))->get();
            $subtotal = 0;

            foreach ($products as $product) {
                $quantity = random_int(1, min(3, $product->stock));
                $price = (int) $product->selling_price; // Pastikan integer
                $itemSubtotal = $price * $quantity;

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $itemSubtotal,
                    'created_at' => $txDate,
                ]);

                $product->decrement('stock', $quantity);

                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => $quantity,
                    'reference_type' => 'App\Models\Transaction',
                    'reference_id' => $transaction->id,
                    'notes' => "Penjualan - {$transaction->transaction_code}",
                    'created_at' => $txDate,
                ]);

                $subtotal += $itemSubtotal;
            }

            // Discount (rounded to nearest 500)
            $discount = fake()->boolean(20) ? round(rand(1000, 5000) / 500) * 500 : 0;
            $totalAmount = max(0, $subtotal - $discount);

            // Amount paid (rounded to nearest 1000)
            if ($paymentMethod === 'utang') {
                $amountPaid = 0;
                $changeAmount = 0;
            } else {
                // Pembulatan ke atas ke kelipatan 1000 terdekat
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

        // ─── Other Seeders ───────────────────────────────────────
        $this->call([
            ExpenseSeeder::class,
            PurchaseSeeder::class,
            CashFlowSeeder::class,
            SettingSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('──────────────────────────────────');
        $this->command->info('Admin : admin@minimarket.com / password');
        $this->command->info('Kasir : kasir1@minimarket.com / password');
        $this->command->info('Products: 27 single + 13 variants = 40 total');
        $this->command->info('Transactions: 50 (30 hari terakhir)');
        $this->command->info('──────────────────────────────────');
    }

    private function seedVariantProducts(Category $fashionCategory): void
    {
        // ── Kaos Polos Premium (3 warna × 3 ukuran = 9 variant) ──
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
                Product::create([
                    'product_group_id' => $tshirtGroup->id,
                    'name' => "Kaos Polos Premium - $color ($size)",
                    'variant_name' => "$color - $size",
                    'category_id' => $fashionCategory->id,
                    'barcode' => 'TS-' . strtoupper(substr($color, 0, 3)) . "-$size-" . str_pad($i++, 3, '0', STR_PAD_LEFT),
                    'purchase_price' => 45000,
                    'selling_price' => 85000,
                    'stock' => random_int(5, 15),
                    'minimum_stock' => 3,
                ]);
            }
        }

        // ── Celana Chino Panjang (4 ukuran) ──
        $pantsGroup = ProductGroup::create([
            'name' => 'Celana Chino Panjang',
            'category_id' => $fashionCategory->id,
            'description' => 'Celana chino bahan stretch',
            'has_variants' => true,
        ]);

        $pantsSizes = ['28', '30', '32', '34'];
        $j = 1;
        foreach ($pantsSizes as $size) {
            Product::create([
                'product_group_id' => $pantsGroup->id,
                'name' => "Celana Chino Panjang (Size $size)",
                'variant_name' => "Size $size",
                'category_id' => $fashionCategory->id,
                'barcode' => 'CHINO-' . $size . '-' . str_pad($j++, 3, '0', STR_PAD_LEFT),
                'purchase_price' => 110000,
                'selling_price' => 185000,
                'stock' => random_int(3, 10),
                'minimum_stock' => 2,
            ]);
        }
    }
}
