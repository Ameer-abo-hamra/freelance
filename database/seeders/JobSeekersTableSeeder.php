<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class JobSeekersTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            DB::table('job_seekers')->insert([
                'username' => $faker->unique()->userName,
                'full_name' => $faker->name,
                'isActive' => $faker->boolean,
                'verificationCode' => $faker->optional()->uuid,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'birth_date' => $faker->date,
                'type' => 'job_seeker',
                'profile_photo' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
