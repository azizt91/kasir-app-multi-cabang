<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = \App\Models\User::first();

        if (!$user) {
            return;
        }

        \App\Models\Expense::create([
            'name' => 'Listrik',
            'description' => 'Bayar Listrik Bulan Ini',
            'amount' => 150000,
            'date' => now()->subDays(5),
            'user_id' => $user->id,
        ]);

        \App\Models\Expense::create([
            'name' => 'ATK',
            'description' => 'Beli Alat Tulis Kantor',
            'amount' => 25000,
            'date' => now()->subDays(2),
            'user_id' => $user->id,
        ]);

        \App\Models\Expense::create([
            'name' => 'Keamanan',
            'description' => 'Iuran Keamanan',
            'amount' => 50000,
            'date' => now(),
            'user_id' => $user->id,
        ]);
    }
}
