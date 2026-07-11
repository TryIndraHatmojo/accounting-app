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
        $productNames = [
            'Kopra',
            'Cengkeh',
            'Kutulak',
            'Damar Batu',
            'Biji Pala',
            'Biji Gebang',
            'Gagang Cengkeh',
            'Kunyit',
            'Mente',
            'Bunga Pala',
        ];

        Company::query()->each(function (Company $company) use ($productNames): void {
            foreach ($productNames as $productName) {
                Product::query()->firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $productName,
                ]);
            }
        });
    }
}
