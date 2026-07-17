<?php

namespace Database\Factories;

use App\Models\ExportDeclaration;
use App\Models\ExportDeclarationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExportDeclarationItem>
 */
class ExportDeclarationItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'export_declaration_id' => ExportDeclaration::factory(),
            'container_number' => fake()->bothify('???? ###### #'),
            'seal_number' => fake()->numerify('#######'),
            'warehouse' => fake()->bothify('???#####'),
            'container_size' => fake()->randomElement(['20 ft', '40 ft', '40 ft HC']),
            'description' => fake()->words(2, true),
            'gross_weight' => fake()->randomFloat(3, 1000, 30000),
            'net_weight' => fake()->randomFloat(3, 1000, 30000),
            'bag_count' => fake()->numberBetween(1, 1000),
            'sort_order' => 0,
        ];
    }
}
