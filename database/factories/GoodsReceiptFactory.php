<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\GoodsReceipt;
use App\Models\ShipmentNotice;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GoodsReceipt>
 */
class GoodsReceiptFactory extends Factory
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
            'shipment_notice_id' => ShipmentNotice::factory(),
            'supplier_id' => Supplier::factory(),
            'document_number' => 'LPB-'.fake()->unique()->numerify('#####'),
            'report_date' => fake()->dateTimeBetween('-1 year'),
            'origin_reference' => fake()->optional()->bothify('PPB-#####'),
            'origin' => fake()->city(),
            'received_date' => fake()->dateTimeBetween('-1 year'),
            'transport_type' => fake()->randomElement(['Truk', 'Kontener', 'Ekspres']),
            'vehicle_number' => fake()->optional()->bothify('?? #### ??'),
            'container_numbers' => fake()->optional()->bothify('???? ###### #'),
            'seal_numbers' => fake()->optional()->bothify('SEAL-#####'),
            'notes' => fake()->optional()->sentence(),
            'recorded_by' => User::factory(),
        ];
    }
}
