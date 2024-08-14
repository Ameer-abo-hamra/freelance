<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['category_name' => 'Web Development'],
            ['category_name' => 'Data Science'],
            ['category_name' => 'Design'],
            ['category_name' => 'Marketing'],
            ['category_name' => 'Writing'],
            ['category_name' => 'Finance'],
            ['category_name' => 'Engineering'],
            ['category_name' => 'Healthcare'],
            ['category_name' => 'Education'],
            ['category_name' => 'Sales'],
        ]);
    }
}
