<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ServiceAppliesTableSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $serviceIds = DB::table('services')->pluck('id');
        $jobSeekerIds = DB::table('job_seekers')->pluck('id');
        $companies = DB::table('companies')->pluck('id');

        foreach (range(1, 10) as $index) {
            DB::table('service_applies')->insert([
                'applyable_type' => 'App\\Models\\JobSeeker',
                'applyable_id' => $jobSeekerIds->random(),
                'service_id' => $serviceIds->random(),
                'offer' => $faker->text(),
                'isAccepted' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        foreach (range(1, 10) as $index) {
            DB::table('service_applies')->insert([
                'applyable_type' => 'App\\Models\\Company',
                'applyable_id' => $companies->random(),
                'service_id' => $serviceIds->random(),
                'offer' => $faker->text(),
                'isAccepted' => $faker->boolean,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
