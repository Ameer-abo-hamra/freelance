<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class FollowsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userTypes = ['App\Models\Customer', 'App\Models\JobSeeker', 'App\Models\Company'];

        foreach (range(1, 30) as $index) {
            DB::table('follows')->insert([
                'followMaker_type' => $faker->randomElement($userTypes),
                'followMaker_id' => $faker->numberBetween(1, 10), // Assuming there are at least 10 users
                'followReciver_type' => $faker->randomElement($userTypes),
                'followReciver_id' => $faker->numberBetween(1, 10), // Assuming there are at least 10 users
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
