<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ReportsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userTypes = ['App\Models\Customer', 'App\Models\JobSeeker', 'App\Models\Company'];
        $reportableTypes = ['App\Models\Post', 'App\Models\Comment'];

        foreach (range(1, 10) as $index) {
            DB::table('reports')->insert([
                'reason' => $faker->optional()->sentence,
                'reporter_type' => $faker->randomElement($userTypes),
                'reporter_id' => $faker->numberBetween(1, 10),
                'reported_type' => $faker->randomElement($reportableTypes),
                'reported_id' => $faker->numberBetween(1, 10),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
