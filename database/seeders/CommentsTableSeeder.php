<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CommentsTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $commentableTypes = ['App\Models\JobSeeker', 'App\Models\Company', 'App\Models\Customer'];

        foreach (range(1, 10) as $index) {
            DB::table('comments')->insert([
                'commentable_type' => $faker->randomElement($commentableTypes),
                'commentable_id' => $faker->numberBetween(1, 10), // Assuming users have ids between 1 and 10
                'post_id' => $faker->numberBetween(1, 10), // Assuming posts have ids between 1 and 10
                'body' => $faker->paragraph,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
