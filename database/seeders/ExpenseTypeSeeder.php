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
        $expenseTypeNames = [
            'Gaji Karyawan',
            'Bongkar Muat',
            'Transportasi',
            'Biaya Kontainer',
            'Administrasi Ekspor',
            'Perlengkapan Gudang',
            'Listrik dan Utilitas',
            'Pengeluaran Perusahaan Lainnya',
        ];

        Company::query()->each(function (Company $company) use ($expenseTypeNames): void {
            foreach ($expenseTypeNames as $expenseTypeName) {
                ExpenseType::query()->firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $expenseTypeName,
                ]);
            }
        });
    }
}
