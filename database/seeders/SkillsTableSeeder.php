<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillsTableSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = DB::table('categories')->pluck('id');

        $skills = [
            'PHP',
            'Laravel',
            'JavaScript',
            'Python',
            'Ruby',
            'Java',
            'C++',
            'C#',
            'HTML',
            'CSS',
        ];

        foreach ($skills as $skill) {
            DB::table('skills')->insert([
                'skill_name' => $skill,
                'category_id' => $categoryIds->random(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
