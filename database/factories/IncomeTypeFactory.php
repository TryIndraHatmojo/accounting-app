<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\IncomeType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncomeType>
 */
class IncomeTypeFactory extends Factory
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
            'name' => fake()->unique()->words(2, true),
        ];
    }
}
