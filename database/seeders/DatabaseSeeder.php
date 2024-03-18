<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Certificate;
use App\Models\Comment;
use App\Models\Comment_like;
use App\Models\Company;
use App\Models\Contact_information;
use App\Models\Job_seeker;
use App\Models\Offer;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        Company::factory(10)->create();
        Job_seeker::factory(10)->create();
        Comment::factory(5)->create();
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Company::create([
        //     "name" => "google",
        //     "establishment_date" => "2024-03-19",
        //     "employee_number" => 30,
        // ]);
        // Comment::create([
        //     "title" => "title",
        //     "body" => "body",
        //     "company_id" => 1
        // ]);
        // Comment_like::create([
        //     "comment_id" => 1,
        //     "company_id" => 1
        // ]);
        // Offer::create([
        //     "title" => "offer title",
        //     "body" => "offer body",
        //     "company_id" => 1,
        //     "position" => "laravel developer"
        // ]);
        // Contact_information::create([
        //     "email" => "ameer@gmail.com",
        //     "phone" => "0935771318",
        //     "address" => "AL-sweida",
        //     "company_id" => 1
        // ]);
        // Job_seeker::create([
        //     "username" => "Ameer314314",
        //     "password" => Hash::make("123456789"),
        //     "full_name" => "Ameer Abo Hamra",
        //     "birth_date" => "2002-10-15"
        // ]);
        // Certificate::create([
        //     "certificate_name" => "IT",
        //     "graduation_date" => "2022-10-10",
        //     "rate" => 99.5,
        //     "job_seeker_id" => 1
        // ]);
        // $front_end = ["html", "css", "js", "flutter", "angular", "vue", "react"];
        // foreach ($front_end as $f) {
        //     Skill::create([
        //         "category" => "front-end",
        //         "skill_name" => $f,
        //     ]);
        // }
        // $back_end = ["php", "java", "js", "laravel", "django", "nodeJs"];
        // foreach ($back_end as $b) {
        //     Skill::create([
        //         "category" => "back-end",
        //         "skill_name" => $b,
        //     ]);
        // }


    }
}
