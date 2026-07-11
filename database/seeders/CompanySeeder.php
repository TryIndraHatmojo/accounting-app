<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            ['name' => 'PT Perusahaan Utama', 'slug' => 'perusahaan-utama'],
            ['name' => 'PT Perusahaan Kedua', 'slug' => 'perusahaan-kedua'],
        ] as $company) {
            Company::query()->updateOrCreate(
                ['slug' => $company['slug']],
                [...$company, 'is_active' => true],
            );
        }
    }
}
