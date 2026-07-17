<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            'Kopra' => 'KPR',
            'Cengkeh' => 'CKG',
            'Kutulak' => 'LAC',
            'Damar Batu' => 'DBT',
            'Biji Pala' => 'BPL',
            'Biji Gebang' => 'GBG',
            'Gagang Cengkeh' => 'GCK',
            'Kunyit' => 'KYT',
            'Mente' => 'MNT',
            'Bunga Pala' => 'BPA',
        ];

        Company::query()->each(function (Company $company) use ($products): void {
            foreach ($products as $productName => $abbreviation) {
                Product::query()->firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $productName,
                ], [
                    'abbreviation' => $abbreviation,
                ]);
            }
        });
    }
}
