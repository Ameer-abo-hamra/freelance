<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Certificate;
use App\Models\Comment;
use App\Models\Comment_like;
use App\Models\Company;
use App\Models\Contact_information;
use App\Models\Customer;
use App\Models\Follow;
use App\Models\Job_seeker;
use App\Models\Post;
use App\Models\Service;
use App\Models\Offer;
use App\Models\Skill;
use Database\Factories\CustomerFactory;
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
        Customer::factory(10)->create();
        // Comment::factory(5)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Company::create([
            "name" => "company1",
            "password" => Hash::make("123456789"),
            "establishment_date" => "2024-03-19",
            "employee_number" => 30,
            "verificationCode" => "test01",
            "email" => "company1@a.com0"
        ]);
        Company::create([
            "name" => "company2",
            "password" => Hash::make("123456789"),
            "establishment_date" => "2024-03-19",
            "employee_number" => 30,
            "verificationCode" => "test01",
            "email" => "company2@a.com0"
        ]);
        Company::create([
            "name" => "company3",
            "password" => Hash::make("123456789"),
            "establishment_date" => "2024-03-19",
            "employee_number" => 30,
            "verificationCode" => "test01",
            "email" => "company3@a.com0"
        ]);
        Post::create([
            "title" => "this post does not show in default",
            "body" => "test for following ",
            "postable_type" => "App\Models\Job_seeker",
            "postable_id" => 1
        ]);

        for ($i = 1; $i <= 3; $i++) {

            for ($j = 1; $j <= 3; $j++) {
                Post::create([
                    "title" => "fsgd",
                    "body" => "gsd",
                    "postable_type" => "App\Models\Job_seeker",
                    "postable_id" => $i+10
                ]);
            }
        }
        for ($i = 1; $i <= 3; $i++) {

            for ($j = 1; $j <= 3; $j++) {
                Post::create([
                    "title" => "fsgd",
                    "body" => "gsd",
                    "postable_type" => "App\Models\Company",
                    "postable_id" => $i+10
                ]);
            }
        }
        Comment::create([
            "title" => "title",
            "body" => "body",
            "post_id" => 1,
            "commentable_type" => "App\Models\Job_seeker",
            "commentable_id" => 1
        ]);
        // Comment_like::create([
        //     "comment_id" => 1,
        //     "company_id" => 1
        // ]);
        Offer::create([
            "author" => "test",
            "title" => "offer title",
            "body" => "offer body",
            "company_id" => 11,
            "position" => "laravel developer",
            "type" => "full-time",
            "details" => "This offer is provided to more than 10 employees"
        ]);
        Offer::create([
            "author" => "test",
            "title" => "offer title",
            "body" => "offer body",
            "company_id" => 1,
            "position" => "laravel developer",
            "type" => "full-time",
            "details" => "This offer is provided to more than 10 employees"
        ]);
        Offer::create([
            "author" => "test",
            "title" => "offer title",
            "body" => "offer body",
            "company_id" => 2,
            "position" => "laravel developer",
            "type" => "full-time",
            "details" => "This offer is provided to more than 10 employees"
        ]);
        // Contact_information::create([
        //     "email" => "ameer@gmail.com",
        //     "phone" => "0935771318",
        //     "address" => "AL-sweida",
        //     "company_id" => 1
        // ]);
        Job_seeker::create([
            "username" => "Ameer314314",
            "password" => Hash::make("123456789"),
            "full_name" => "Ameer Abo Hamra",
            "birth_date" => "2002-10-15",
            "verificationCode" => "test01",
            "email" => "jobseeker@h.com"
        ]);
        Job_seeker::create([
            "username" => "Ameer31431",
            "password" => Hash::make("123456789"),
            "full_name" => "Ameer Abo Hamra",
            "birth_date" => "2002-10-15",
            "verificationCode" => "test01",
            "email" => "jobseeker2@h.com"
        ]);
        Job_seeker::create([
            "username" => "Ameer3143",
            "password" => Hash::make("123456789"),
            "full_name" => "Ameer Abo Hamra",
            "birth_date" => "2002-10-15",
            "verificationCode" => "test01",
            "email" => "jobseeker3@h.com"
        ]);
        Certificate::create([
            "certificate_name" => "IT",
            "graduation_date" => "2022-10-10",
            "rate" => 99.5,
            "job_seeker_id" => 1
        ]);

        Customer::create([
            "username" => "customer1",
            "full_name" => "ameer abo hamra",
            "isActive" => 0,
            "verificationCode" => "kjhn",
            "email" => "customer1@qd.com",
            "password" => Hash::make("2345676543"),
            "wallet" => "10000",
            "birth_date" => "2022-10-10",
        ]);
        Customer::create([
            "username" => "customer2",
            "full_name" => "ameer abo hamra",
            "isActive" => 0,
            "verificationCode" => "kjhn",
            "email" => "customer2@qd.com",
            "password" => Hash::make("2345676543"),
            "wallet" => "10000",
            "birth_date" => "2022-10-10",
        ]);
        Customer::create([
            "username" => "customer3",
            "full_name" => "ameer abo hamra",
            "isActive" => 0,
            "verificationCode" => "kjhn",
            "email" => "customer3@qd.com",
            "password" => Hash::make("2345676543"),
            "wallet" => "10000",
            "birth_date" => "2022-10-10",
        ]);
        Service::create([
            "description" => "this is wg sd",
            "customer_id" => 1
        ]);

        Follow::create([
            "followMaker_type" => "app\Models\Company",
            "followMaker_id" => 1,
            "followReciver_type" => "app\Models\Job_seeker",
            "followReciver_id" => 1
        ]);
        Follow::create([
            "followMaker_type" => "app\Models\Company",
            "followMaker_id" => 1,
            "followReciver_type" => "app\Models\Job_seeker",
            "followReciver_id" => 2
        ]);
        Follow::create([
            "followMaker_type" => "app\Models\Company",
            "followMaker_id" => 2,
            "followReciver_type" => "app\Models\Job_seeker",
            "followReciver_id" => 3
        ]);
        for ($i = 1; $i <= 3; $i++) {

            for ($j = 1; $j <= 3; $j++) {
                Follow::create([
                    "followMaker_type" => "app\Models\Company",
                    "followMaker_id" => $i+10,
                    "followReciver_type" => "app\Models\Job_seeker",
                    "followReciver_id" => $j+10
                ]);
            }
        }

    }
}
