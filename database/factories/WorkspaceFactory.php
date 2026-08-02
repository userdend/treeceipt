<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workspace>
 */
class WorkspaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement(['personal', 'business']),
            'tax_no' => fake()->numerify('TAX-#####'),
            'registration_no' => fake()->numerify('REG-#####'),
            'is_active' => true,
        ];
    }
}
