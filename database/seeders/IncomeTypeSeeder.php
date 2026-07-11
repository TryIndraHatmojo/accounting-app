<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\IncomeType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IncomeTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incomeTypeNames = ['Penjualan', 'Uang Masuk Lainnya'];

        Company::query()->each(function (Company $company) use ($incomeTypeNames): void {
            foreach ($incomeTypeNames as $incomeTypeName) {
                IncomeType::query()->firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $incomeTypeName,
                ]);
            }
        });
    }
}
