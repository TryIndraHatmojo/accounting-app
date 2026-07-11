<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
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
            'expense_date' => fake()->dateTimeBetween('-1 year'),
            'expense_type_id' => ExpenseType::factory(),
            'recorded_by' => User::factory(),
            'description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 10_000, 100_000_000),
        ];
    }
}
