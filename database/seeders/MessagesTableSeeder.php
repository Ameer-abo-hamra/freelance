<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MessagesTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $userTypes = ['App\Models\Customer', 'App\Models\JobSeeker', 'App\Models\Company'];

        foreach (range(1, 100) as $index) {
            DB::table('messages')->insert([
                'sender_type' => $faker->randomElement($userTypes),
                'sender_id' => $faker->numberBetween(1, 10), // Assuming there are at least 10 users
                'reciver_type' => $faker->randomElement($userTypes),
                'reciver_id' => $faker->numberBetween(1, 10), // Assuming there are at least 10 users
                'content' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
