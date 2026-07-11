<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supplierNames = ['Hangtua', 'Rudy', 'Vivi Larat', 'HR', 'Alex'];

        Company::query()->each(function (Company $company) use ($supplierNames): void {
            foreach ($supplierNames as $supplierName) {
                Supplier::query()->firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $supplierName,
                ]);
            }
        });
    }
}
