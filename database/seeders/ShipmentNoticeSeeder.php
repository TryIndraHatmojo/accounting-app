<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use App\Models\ShipmentNotice;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShipmentNoticeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recorderId = User::query()
            ->where('email', 'gudang@example.com')
            ->valueOrFail('id');

        Company::query()->each(function (Company $company) use ($recorderId): void {
            foreach ($this->shipmentData() as $shipmentData) {
                $productId = Product::query()
                    ->whereBelongsTo($company)
                    ->where('name', $shipmentData['product'])
                    ->valueOrFail('id');

                $shipmentNotice = ShipmentNotice::query()->updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'document_number' => $shipmentData['document_number'],
                    ],
                    [
                        'loading_date' => $shipmentData['loading_date'],
                        'origin' => 'Kupang, NTT',
                        'departure_date' => $shipmentData['departure_date'],
                        'vehicle_number' => $shipmentData['vehicle_number'],
                        'container_numbers' => $shipmentData['container_number'],
                        'seal_numbers' => $shipmentData['seal_number'],
                        'notes' => 'Data contoh Pemberitahuan Pengiriman Barang.',
                        'recorded_by' => $recorderId,
                    ],
                );

                $shipmentNotice->items()->updateOrCreate(
                    ['sort_order' => 0],
                    [
                        'product_id' => $productId,
                        'section_name' => $shipmentData['section_name'],
                        'package_count' => $shipmentData['package_count'],
                        'initial_weight' => $shipmentData['initial_weight'],
                        'final_weight' => $shipmentData['shipment_weight'],
                    ],
                );
            }
        });
    }

    /**
     * @return array<int, array{
     *     document_number: string,
     *     product: string,
     *     loading_date: string,
     *     departure_date: string,
     *     vehicle_number: string,
     *     container_number: string,
     *     seal_number: string,
     *     section_name: string,
     *     package_count: int,
     *     initial_weight: float,
     *     shipment_weight: float
     * }>
     */
    private function shipmentData(): array
    {
        return [
            [
                'document_number' => 'SINDO-01',
                'product' => 'Kutulak',
                'loading_date' => '2026-06-27',
                'departure_date' => '2026-06-28',
                'vehicle_number' => 'DH 8101 UA',
                'container_number' => 'SIPU 777134 4',
                'seal_number' => 'SEAL-001',
                'section_name' => 'Pengiriman Express',
                'package_count' => 8,
                'initial_weight' => 400.000,
                'shipment_weight' => 398.000,
            ],
            [
                'document_number' => 'SINDO-02',
                'product' => 'Kutulak',
                'loading_date' => '2026-06-30',
                'departure_date' => '2026-07-01',
                'vehicle_number' => 'DH 8102 UA',
                'container_number' => 'SIPU 777135 3',
                'seal_number' => 'SEAL-002',
                'section_name' => 'Kontener 1',
                'package_count' => 8,
                'initial_weight' => 305.000,
                'shipment_weight' => 304.000,
            ],
            [
                'document_number' => 'SINDO-03',
                'product' => 'Kutulak',
                'loading_date' => '2026-07-05',
                'departure_date' => '2026-07-06',
                'vehicle_number' => 'DH 8103 UA',
                'container_number' => 'SIPU 777136 2',
                'seal_number' => 'SEAL-003',
                'section_name' => 'Kontener 1',
                'package_count' => 64,
                'initial_weight' => 4800.000,
                'shipment_weight' => 4750.000,
            ],
            [
                'document_number' => 'SINDO-04',
                'product' => 'Biji Gebang',
                'loading_date' => '2026-04-03',
                'departure_date' => '2026-04-04',
                'vehicle_number' => 'DH 8104 UA',
                'container_number' => 'SIPU 777137 1',
                'seal_number' => 'SEAL-004',
                'section_name' => 'Pengiriman Express',
                'package_count' => 3,
                'initial_weight' => 190.000,
                'shipment_weight' => 187.000,
            ],
            [
                'document_number' => 'SINDO-05',
                'product' => 'Kunyit',
                'loading_date' => '2026-05-16',
                'departure_date' => '2026-05-17',
                'vehicle_number' => 'DH 8105 UA',
                'container_number' => 'SIPU 777138 0',
                'seal_number' => 'SEAL-005',
                'section_name' => 'Pengiriman Express',
                'package_count' => 3,
                'initial_weight' => 52.000,
                'shipment_weight' => 51.000,
            ],
            [
                'document_number' => 'SINDO-06',
                'product' => 'Kunyit',
                'loading_date' => '2026-06-19',
                'departure_date' => '2026-06-20',
                'vehicle_number' => 'DH 8106 UA',
                'container_number' => 'SIPU 777139 9',
                'seal_number' => 'SEAL-006',
                'section_name' => 'Kontener 1',
                'package_count' => 85,
                'initial_weight' => 3450.000,
                'shipment_weight' => 3430.000,
            ],
            [
                'document_number' => 'SINDO-07',
                'product' => 'Kunyit',
                'loading_date' => '2026-06-19',
                'departure_date' => '2026-06-20',
                'vehicle_number' => 'DH 8107 UA',
                'container_number' => 'SIPU 777140 7',
                'seal_number' => 'SEAL-007',
                'section_name' => 'Kontener 2',
                'package_count' => 98,
                'initial_weight' => 3140.000,
                'shipment_weight' => 3125.000,
            ],
        ];
    }
}
