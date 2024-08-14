<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesTableSeeder extends Seeder
{
    public function run(): void
    {
        $customerIds = DB::table('customers')->pluck('id');

        foreach (range(1, 10) as $index) {
            DB::table('services')->insert([
                'description' => 'Service description ' . $index,
                'price' => rand(500, 2000),
                'customer_id' => $customerIds->random(),
                'is_accepted' => rand(0, 1),
                'state' => 'open',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
