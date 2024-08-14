<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = DB::table('categories')->pluck('id');

        DB::table('types')->insert([
            ['type_name' => 'Full-Time', 'category_id' => $categoryIds->random()],
            ['type_name' => 'Part-Time', 'category_id' => $categoryIds->random()],
            ['type_name' => 'Contract', 'category_id' => $categoryIds->random()],
            ['type_name' => 'Freelance', 'category_id' => $categoryIds->random()],
            ['type_name' => 'Internship', 'category_id' => $categoryIds->random()],
            ['type_name' => 'Temporary', 'category_id' => $categoryIds->random()],
            ['type_name' => 'Remote', 'category_id' => $categoryIds->random()],
            ['type_name' => 'On-Site', 'category_id' => $categoryIds->random()],
            ['type_name' => 'Volunteer', 'category_id' => $categoryIds->random()],
            ['type_name' => 'Apprenticeship', 'category_id' => $categoryIds->random()],
        ]);
    }
}
