<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class EssentialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin Minimarket',
            'email' => 'admin@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create kasir users
        User::create([
            'name' => 'Kasir 1',
            'email' => 'kasir1@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Kasir 2',
            'email' => 'kasir2@minimarket.com',
            'password' => bcrypt('password'),
            'role' => 'kasir',
            'email_verified_at' => now(),
        ]);

        // Call SettingSeeder for default store settings
        $this->call([
            SettingSeeder::class,
        ]);

        $this->command->info('Essential data seeded successfully (Users + Settings). No dummy data.');
    }
}
