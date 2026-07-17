<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            ExpenseTypeSeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            IncomeTypeSeeder::class,
            ShipmentNoticeSeeder::class,
            GoodsReceiptSeeder::class,
            ExportDeclarationSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
