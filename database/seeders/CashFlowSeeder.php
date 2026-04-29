<?php

namespace Database\Seeders;

use App\Models\CashFlow;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class CashFlowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        $users = User::all();
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please seed users first.');
            return;
        }

        // Generate 15 dummy cash flows for the last 30 days
        for ($i = 0; $i < 15; $i++) {
            $type = $faker->randomElement(['in', 'out']);
            
            // Generate categories based on type
            if ($type === 'in') {
                $category = $faker->randomElement(['Penambahan Modal', 'Pendapatan Lain', 'Suntikan Dana']);
                $amount = $faker->randomElement([500000, 1000000, 2000000, 5000000]);
            } else {
                $category = $faker->randomElement(['Penarikan Modal (Prive)', 'Pengeluaran Lain', 'Sumbangan']);
                $amount = $faker->randomElement([100000, 250000, 500000, 1000000]);
            }

            CashFlow::create([
                'user_id' => $users->random()->id,
                'date' => Carbon::now()->subDays(random_int(0, 30))->format('Y-m-d'),
                'type' => $type,
                'category' => $category,
                'amount' => $amount,
                'note' => $faker->boolean(70) ? $faker->sentence(4) : null,
            ]);
        }
    }
}
