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

        \App\Models\User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        Company::create([
            "name" => "google",
            "password" => Hash::make("123456789"),
            "establishment_date" => "2024-03-19",
            "employee_number" => 30,
            "verificationCode" => "test01",
            "email" => "aa@a.com0"
        ]);

        Comment::create([
            "title" => "title",
            "body" => "body",
            "company_id" => 1,
            "post_id" => 1

        ]);
        Comment_like::create([
            "comment_id" => 1,
            "company_id" => 1
        ]);
        Offer::create([
            "author" => "test",
            "title" => "offer title",
            "body" => "offer body",
            "company_id" => 1,
            "position" => "laravel developer",
            "type"=> "full-time",
            "details"=> "This offer is provided to more than 10 employees"
        ]);
        Contact_information::create([
            "email" => "ameer@gmail.com",
            "phone" => "0935771318",
            "address" => "AL-sweida",
            "company_id" => 1
        ]);
        Job_seeker::create([
            "username" => "Ameer314314",
            "password" => Hash::make("123456789"),
            "full_name" => "Ameer Abo Hamra",
            "birth_date" => "2002-10-15",
            "verificationCode" => "test01",
            "email" => "a@h.com"
        ]);
        Certificate::create([
            "certificate_name" => "IT",
            "graduation_date" => "2022-10-10",
            "rate" => 99.5,
            "job_seeker_id" => 1
        ]);
        $front_end = ["html", "css", "js", "flutter", "angular", "vue", "react"];
        foreach ($front_end as $f) {
            Skill::create([

                "category" => "programming",
                "type" => "front-end",
                "skill_name" => $f,
            ]);
        }
        $back_end = ["php", "java", "js", "laravel", "django", "nodeJs"];
        foreach ($back_end as $b) {
            Skill::create([
                "category" => "programming",
                "type" => "back-end",
                "skill_name" => $b,
            ]);
        }

        $architecture_desginer = ["autocad", "reveit", "sketchUp"];
        foreach ($architecture_desginer as $a) {
            Skill::create([
                "category" => "architecture",
                "type" => "architecture_desginer",
                "skill_name" => $a
            ]);
        }

        $project_manager = ["planning", "schaduling", "budgeting", "building_codes", "regulations"];

        foreach ($project_manager as $p) {
            Skill::create([
                "category" => "architecture",
                "type" => "project_manager",
                "skill_name" => $p
            ]);
        }
        $interior_desginer = ["autocad", "3dMax", "adobe_photoshop"];
        foreach ($interior_desginer as $i) {
            Skill::create([
                "category" => "architecture",
                "type" => "interior_desginer",
                "skill_name" => $i
            ]);
        }

        $financial_analyst = ["exel"];
        foreach ($financial_analyst as $f) {
            Skill::create([
                "category" => "financial",
                "type" => "financial_analyst",
                "skill_name" => $f
            ]);
        }
        $marketing_cordenator = ["marketing_principles", "digital_marketing"];
        foreach ($marketing_cordenator as $m) {
            Skill::create([
                "category" => "financial",
                "type" => "marketing_cordenator",
                "skill_name" => $m
            ]);
        }
        $buisness_development_manager = ["exel", "good_writer"];
        foreach ($buisness_development_manager as $b) {
            Skill::create([
                "category" => "financial",
                "type" => "buisness_development_manager",
                "skill_name" => $b
            ]);
        }
    }
}
