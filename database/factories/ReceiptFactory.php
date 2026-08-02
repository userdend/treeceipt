<?php

namespace Database\Factories;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Receipt>
 */
class ReceiptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'merchant' => fake()->company(),
            'total' => fake()->randomFloat(2, 5, 500),
            'currency' => 'MYR',
            'receipt_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'file_path' => 'receipts/' . fake()->uuid() . '.jpg',
            'ocr_data' => json_encode(['raw_text' => fake()->sentence()]),
            'is_active' => true,
        ];
    }
}
