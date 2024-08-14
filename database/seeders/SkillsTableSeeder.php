<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillsTableSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = DB::table('categories')->pluck('id');

        DB::table('skills')->insert([
            ['skill_name' => 'PHP', 'category_id' => $categoryIds->random()],
            ['skill_name' => 'Laravel', 'category_id' => $categoryIds->random()],
            ['skill_name' => 'Python', 'category_id' => $categoryIds->random()],
            ['skill_name' => 'Data Analysis', 'category_id' => $categoryIds->random()],
            ['skill_name' => 'Graphic Design', 'category_id' => $categoryIds->random()],
            ['skill_name' => 'SEO', 'category_id' => $categoryIds->random()],
            ['skill_name' => 'Content Writing', 'category_id' => $categoryIds->random()],
            ['skill_name' => 'Accounting', 'category_id' => $categoryIds->random()],
            ['skill_name' => 'Project Management', 'category_id' => $categoryIds->random()],
            ['skill_name' => 'Sales Strategy', 'category_id' => $categoryIds->random()],
        ]);
    }
}
