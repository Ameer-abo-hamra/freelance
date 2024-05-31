<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Hash;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "username" => fake()->userName(),
            "full_name" => fake()->name(),
            "isActive" => false,
            "verificationCode" => fake()->text(6),
            "email" => fake()->email(),
            "password" => Hash::make(fake()->password(8, 20)),
            "wallet" => fake()->numberBetween(1000, 10000),
            "profile_photo" => fake()->text(),
            "birth_date" => fake()->date(),
        ];
    }
}
