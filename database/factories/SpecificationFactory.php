<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specification>
 */
class SpecificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'input_type' => fake()->randomElement(['text', 'number', 'select']),
            'measure' => fake()->randomElement(['GB', 'TB', 'L', 'W', 'inch', null]),
            'description' => fake()->sentence(),
        ];
    }
}
