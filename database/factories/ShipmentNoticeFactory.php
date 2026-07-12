<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ShipmentNotice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipmentNotice>
 */
class ShipmentNoticeFactory extends Factory
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
            'document_number' => 'PPB-'.fake()->unique()->numerify('#####'),
            'loading_date' => fake()->dateTimeBetween('-1 year'),
            'origin' => fake()->city(),
            'departure_date' => fake()->dateTimeBetween('-1 year'),
            'vehicle_number' => fake()->optional()->bothify('?? #### ??'),
            'container_numbers' => fake()->optional()->bothify('???? ###### #'),
            'seal_numbers' => fake()->optional()->bothify('SEAL-#####'),
            'notes' => fake()->optional()->sentence(),
            'recorded_by' => User::factory(),
        ];
    }
}
