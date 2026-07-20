<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ExportDeclaration;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExportDeclarationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recorderId = User::query()
            ->where('email', 'akuntan@example.com')
            ->valueOrFail('id');

        Company::query()->each(function (Company $company) use ($recorderId): void {
            foreach ($this->exportData() as $exportData) {
                $exportDeclaration = ExportDeclaration::query()->updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'peb_number' => $exportData['peb_number'],
                    ],
                    [
                        'document_date' => $exportData['document_date'],
                        'exporter_name' => $company->name,
                        'invoice_number' => $exportData['invoice_number'],
                        'container_quantity' => 1,
                        'container_size' => $exportData['container_size'],
                        'destination_port' => $exportData['destination_port'],
                        'attachments' => null,
                        'notes' => 'Data contoh Pemberitahuan Ekspor Barang.',
                        'recorded_by' => $recorderId,
                    ],
                );

                $exportDeclaration->items()->updateOrCreate(
                    ['sort_order' => 0],
                    [
                        'product_id' => $company->products()
                            ->where('name', $exportData['description'])
                            ->valueOrFail('id'),
                        'container_number' => $exportData['container_number'],
                        'seal_number' => $exportData['seal_number'],
                        'warehouse' => $exportData['warehouse'],
                        'container_size' => $exportData['container_size'],
                        'description' => $exportData['description'],
                        'gross_weight' => $exportData['gross_weight'],
                        'net_weight' => $exportData['net_weight'],
                        'bag_count' => $exportData['bag_count'],
                    ],
                );
            }
        });
    }

    /**
     * @return array<int, array{
     *     document_date: string,
     *     peb_number: string,
     *     invoice_number: string,
     *     container_size: string,
     *     destination_port: string,
     *     container_number: string,
     *     seal_number: string,
     *     warehouse: string,
     *     description: string,
     *     gross_weight: float,
     *     net_weight: float,
     *     bag_count: int
     * }>
     */
    private function exportData(): array
    {
        return [
            [
                'document_date' => '2026-04-28',
                'peb_number' => '41.UVC.04-26',
                'invoice_number' => '001/GBG/RSS-INV/04/26',
                'container_size' => '20 ft',
                'destination_port' => 'India',
                'container_number' => 'SIKU 307838 5',
                'seal_number' => '0087588',
                'warehouse' => 'UVC00059',
                'description' => 'Biji Gebang',
                'gross_weight' => 185.000,
                'net_weight' => 180.000,
                'bag_count' => 3,
            ],
            [
                'document_date' => '2026-06-25',
                'peb_number' => '42.UVC.06-26',
                'invoice_number' => '002/KYT/RSS-INV/06/26',
                'container_size' => '20 ft',
                'destination_port' => 'Malaysia',
                'container_number' => 'SIKU 307839 4',
                'seal_number' => '0087589',
                'warehouse' => 'UVC00060',
                'description' => 'Kunyit',
                'gross_weight' => 3422.300,
                'net_weight' => 3380.000,
                'bag_count' => 85,
            ],
            [
                'document_date' => '2026-07-10',
                'peb_number' => '43.UVC.07-26',
                'invoice_number' => '003/LAC/RSS-INV/07/26',
                'container_size' => '40 ft',
                'destination_port' => 'Vietnam',
                'container_number' => 'SIKU 307840 2',
                'seal_number' => '0087590',
                'warehouse' => 'UVC00061',
                'description' => 'Kutulak',
                'gross_weight' => 4736.000,
                'net_weight' => 4680.000,
                'bag_count' => 64,
            ],
        ];
    }
}
