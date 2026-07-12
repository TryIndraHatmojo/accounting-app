<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ShipmentNotice;
use App\Models\ShipmentNoticeItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShipmentNoticeItem>
 */
class ShipmentNoticeItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shipment_notice_id' => ShipmentNotice::factory(),
            'product_id' => Product::factory(),
            'section_name' => fake()->randomElement(['Pengiriman Express', 'Kontener 1']),
            'package_count' => fake()->numberBetween(1, 200),
            'initial_weight' => fake()->randomFloat(3, 100, 20_000),
            'final_weight' => fake()->randomFloat(3, 100, 20_000),
            'sort_order' => 1,
        ];
    }
}
