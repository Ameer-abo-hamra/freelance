<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class CompaniesTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 10) as $index) {
            DB::table('companies')->insert([
                'name' => $faker->unique()->company,
                'password' => Hash::make('password'),
                'email' => $faker->unique()->companyEmail,
                'isActive' => $faker->boolean,
                'verificationCode' => $faker->optional()->uuid,
                'establishment_date' => $faker->date,
                'type' => 'company',
                'employee_number' => $faker->numberBetween(10, 100),
                'profile_photo' => $faker->optional()->imageUrl,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
