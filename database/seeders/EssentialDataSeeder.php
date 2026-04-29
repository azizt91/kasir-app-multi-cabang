<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class EssentialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Branches
        $branch1 = Branch::create([
            'name' => 'Cabang Utama (Pusat)',
            'address' => 'Jl. Sudirman No. 1, Jakarta',
            'phone' => '021-12345678',
            'is_active' => true,
        ]);

        $branch2 = Branch::create([
            'name' => 'Cabang Bandung',
            'address' => 'Jl. Asia Afrika No. 10, Bandung',
            'phone' => '022-87654321',
            'is_active' => true,
        ]);

        // 2. Create Warehouses
        Warehouse::create([
            'branch_id' => $branch1->id,
            'name' => 'Gudang Pusat',
            'location' => 'Belakang Toko Utama',
            'is_active' => true,
        ]);

        Warehouse::create([
            'branch_id' => $branch2->id,
            'name' => 'Gudang Bandung',
            'location' => 'Lantai 2 Toko Bandung',
            'is_active' => true,
        ]);

        // 3. Create Superadmin (No branch_id, access to all)
        User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'branch_id' => null,
            'email_verified_at' => now(),
        ]);

        // 4. Create Admin for Branch 1
        User::create([
            'name' => 'Admin Pusat',
            'email' => 'admin.pusat@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'branch_id' => $branch1->id,
            'email_verified_at' => now(),
        ]);

        // 5. Create Kasir users assigned to branches
        User::create([
            'name' => 'Kasir Pusat',
            'email' => 'kasir1@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'branch_id' => $branch1->id,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Kasir Bandung',
            'email' => 'kasir2@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'branch_id' => $branch2->id,
            'email_verified_at' => now(),
        ]);

        // Call SettingSeeder for default store settings
        $this->call([
            SettingSeeder::class,
        ]);

        $this->command->info('Essential data seeded successfully (Branches, Warehouses, Users + Settings). No dummy data.');
    }
}
