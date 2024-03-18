<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job_seeker>
 */
class Job_seekerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "username" => fake()->unique()->userName(),
            "password" => Hash::make("123456789"),
            "full_name" => fake()->name(),
            "birth_date" => fake()->date(),
        ];
    }
}
