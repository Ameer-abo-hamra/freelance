<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PostsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userTypes = ['App\Models\Customer', 'App\Models\JobSeeker', 'App\Models\Company'];

        foreach (range(1, 10) as $index) {
            DB::table('posts')->insert([
                'title' => $faker->sentence,
                'body' => $faker->paragraph,
                'photo' => $faker->optional()->imageUrl,
                'postable_type' => $faker->randomElement($userTypes),
                'postable_id' => $faker->numberBetween(1, 10), // Assuming you have at least 10 users of each type
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
