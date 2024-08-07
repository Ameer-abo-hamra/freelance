<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Hash;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "password" => Hash::make(fake()->password(8,15)),
            "establishment_date" => fake()->date('Y-m-d'),
            "employee_number" => fake()->numberBetween(0,1000),
            "verificationCode"=>fake()->text("6"),
            "email"=>fake()->email(),
        ];
    }
}
