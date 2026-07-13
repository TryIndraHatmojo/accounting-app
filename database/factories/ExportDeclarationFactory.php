<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ExportDeclaration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExportDeclaration>
 */
class ExportDeclarationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'document_date' => fake()->dateTimeBetween('-1 year'),
            'exporter_name' => fake()->company(),
            'peb_number' => fake()->unique()->bothify('##.???.##-##'),
            'invoice_number' => fake()->unique()->bothify('###/???/???-INV/##/##'),
            'container_quantity' => fake()->numberBetween(1, 10),
            'container_size' => fake()->randomElement(['20ft', '40ft', '40HC']),
            'destination_port' => fake()->country(),
            'attachments' => null,
            'notes' => fake()->optional()->sentence(),
            'recorded_by' => User::factory(),
        ];
    }
}
