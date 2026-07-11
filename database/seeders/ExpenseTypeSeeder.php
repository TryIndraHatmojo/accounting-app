<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\ExpenseType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::query()->each(function (Company $company): void {
            foreach (['Gaji Karyawan', 'Pengeluaran Perusahaan Lainnya'] as $expenseTypeName) {
                ExpenseType::query()->firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $expenseTypeName,
                ]);
            }
        });
    }
}
