<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\ShipmentNotice;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class GoodsReceiptSeeder extends Seeder
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
            foreach ($this->receiptData() as $receiptData) {
                $shipmentNotice = ShipmentNotice::query()
                    ->whereBelongsTo($company)
                    ->where('document_number', $receiptData['shipment_document_number'])
                    ->firstOrFail();
                $supplierId = Supplier::query()
                    ->whereBelongsTo($company)
                    ->where('name', $receiptData['supplier'])
                    ->valueOrFail('id');
                $productId = Product::query()
                    ->whereBelongsTo($company)
                    ->where('name', $receiptData['product'])
                    ->valueOrFail('id');

                $goodsReceipt = GoodsReceipt::query()->updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'document_number' => $receiptData['document_number'],
                    ],
                    [
                        'shipment_notice_id' => $shipmentNotice->id,
                        'supplier_id' => $supplierId,
                        'report_date' => $receiptData['received_date'],
                        'origin_reference' => $shipmentNotice->document_number,
                        'origin' => $shipmentNotice->origin,
                        'received_date' => $receiptData['received_date'],
                        'transport_type' => 'Truk',
                        'vehicle_number' => $shipmentNotice->vehicle_number,
                        'container_numbers' => $shipmentNotice->container_numbers,
                        'seal_numbers' => $shipmentNotice->seal_numbers,
                        'notes' => 'Data contoh Laporan Penerimaan Barang untuk Stock Barang.',
                        'recorded_by' => $recorderId,
                    ],
                );

                $goodsReceipt->items()->updateOrCreate(
                    ['sort_order' => 0],
                    [
                        'product_id' => $productId,
                        'section_name' => $receiptData['section_name'],
                        'package_count' => $receiptData['package_count'],
                        'initial_weight' => $receiptData['initial_weight'],
                        'final_weight' => $receiptData['final_weight'],
                    ],
                );
            }
        });
    }

    /**
     * @return array<int, array{
     *     document_number: string,
     *     shipment_document_number: string,
     *     supplier: string,
     *     product: string,
     *     received_date: string,
     *     section_name: string,
     *     package_count: int,
     *     initial_weight: float,
     *     final_weight: float
     * }>
     */
    private function receiptData(): array
    {
        return [
            [
                'document_number' => 'SBY.NNA.101',
                'shipment_document_number' => 'SINDO-01',
                'supplier' => 'Hangtua',
                'product' => 'Kutulak',
                'received_date' => '2026-06-30',
                'section_name' => 'Pengiriman Express',
                'package_count' => 8,
                'initial_weight' => 398.000,
                'final_weight' => 396.200,
            ],
            [
                'document_number' => 'SBY.NNA.102',
                'shipment_document_number' => 'SINDO-02',
                'supplier' => 'Rudy',
                'product' => 'Kutulak',
                'received_date' => '2026-07-03',
                'section_name' => 'Kontener 1',
                'package_count' => 8,
                'initial_weight' => 304.000,
                'final_weight' => 302.300,
            ],
            [
                'document_number' => 'SBY.NNA.103',
                'shipment_document_number' => 'SINDO-03',
                'supplier' => 'Vivi Larat',
                'product' => 'Kutulak',
                'received_date' => '2026-07-08',
                'section_name' => 'Kontener 1',
                'package_count' => 64,
                'initial_weight' => 4750.000,
                'final_weight' => 4736.000,
            ],
            [
                'document_number' => 'SBY.NNA.104',
                'shipment_document_number' => 'SINDO-04',
                'supplier' => 'HR',
                'product' => 'Biji Gebang',
                'received_date' => '2026-04-06',
                'section_name' => 'Pengiriman Express',
                'package_count' => 3,
                'initial_weight' => 187.000,
                'final_weight' => 185.000,
            ],
            [
                'document_number' => 'SBY.NNA.105',
                'shipment_document_number' => 'SINDO-05',
                'supplier' => 'Alex',
                'product' => 'Kunyit',
                'received_date' => '2026-05-19',
                'section_name' => 'Pengiriman Express',
                'package_count' => 3,
                'initial_weight' => 51.000,
                'final_weight' => 49.800,
            ],
            [
                'document_number' => 'SBY.NNA.106',
                'shipment_document_number' => 'SINDO-06',
                'supplier' => 'Hangtua',
                'product' => 'Kunyit',
                'received_date' => '2026-06-22',
                'section_name' => 'Kontener 1',
                'package_count' => 85,
                'initial_weight' => 3430.000,
                'final_weight' => 3422.300,
            ],
            [
                'document_number' => 'SBY.NNA.107',
                'shipment_document_number' => 'SINDO-07',
                'supplier' => 'Rudy',
                'product' => 'Kunyit',
                'received_date' => '2026-06-22',
                'section_name' => 'Kontener 2',
                'package_count' => 98,
                'initial_weight' => 3125.000,
                'final_weight' => 3115.000,
            ],
        ];
    }
}
