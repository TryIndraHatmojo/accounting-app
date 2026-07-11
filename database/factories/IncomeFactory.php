<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Income;
use App\Models\IncomeType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Income>
 */
class IncomeFactory extends Factory
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
            'income_date' => fake()->dateTimeBetween('-1 year'),
            'income_type_id' => IncomeType::factory(),
            'recorded_by' => User::factory(),
            'description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 10_000, 100_000_000),
        ];
    }
}
