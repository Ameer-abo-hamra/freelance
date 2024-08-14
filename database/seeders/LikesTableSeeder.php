<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LikesTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $likeableTypes = ['App\Models\Post', 'App\Models\Comment'];
        $userTypes = ['App\Models\Customer', 'App\Models\JobSeeker', 'App\Models\Company'];

        foreach (range(1, 10) as $index) {
            DB::table('likes')->insert([
                'likeable_type' => $faker->randomElement($likeableTypes),
                'likeable_id' => $faker->numberBetween(1, 10), // Assuming posts/comments have ids between 1 and 10
                'user_type' => $faker->randomElement($userTypes),
                'user_id' => $faker->numberBetween(1, 10), // Assuming users have ids between 1 and 10
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
