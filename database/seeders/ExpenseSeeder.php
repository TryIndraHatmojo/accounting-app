<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recorderId = User::query()
            ->where('email', 'akuntan@accounting.test')
            ->valueOrFail('id');

        Company::query()->each(function (Company $company) use ($recorderId): void {
            foreach ($this->expenseData() as $expenseData) {
                $expenseTypeId = ExpenseType::query()
                    ->whereBelongsTo($company)
                    ->where('name', $expenseData['expense_type'])
                    ->valueOrFail('id');

                Expense::query()->updateOrCreate(
                    [
                        'company_id' => $company->id,
                        'description' => $expenseData['description'],
                    ],
                    [
                        'expense_date' => $expenseData['expense_date'],
                        'expense_type_id' => $expenseTypeId,
                        'batch_number' => $expenseData['batch_number'],
                        'batch_type' => $expenseData['batch_type'],
                        'recorded_by' => $recorderId,
                        'item_code' => $expenseData['item_code'],
                        'quantity' => $expenseData['quantity'],
                        'unit_price' => $expenseData['unit_price'],
                        'cost_category' => $expenseData['cost_category'],
                        'amount' => $expenseData['amount'],
                    ],
                );
            }
        });
    }

    /**
     * @return array<int, array{
     *     expense_date: string,
     *     expense_type: string,
     *     description: string,
     *     batch_number: ?string,
     *     batch_type: ?string,
     *     item_code: ?string,
     *     quantity: ?float,
     *     unit_price: ?float,
     *     cost_category: ?string,
     *     amount: float
     * }>
     */
    private function expenseData(): array
    {
        return [
            [
                'expense_date' => '2026-04-06',
                'expense_type' => 'Bongkar Muat',
                'description' => 'Bongkar 3 koli Biji Gebang',
                'batch_number' => '1.1',
                'batch_type' => Expense::BATCH_TYPE_LOCAL,
                'item_code' => 'GBG-060426',
                'quantity' => 3,
                'unit_price' => 50000,
                'cost_category' => Expense::COST_CATEGORY_OPERATIONAL,
                'amount' => 150000,
            ],
            [
                'expense_date' => '2026-05-19',
                'expense_type' => 'Transportasi',
                'description' => 'Transportasi penerimaan Kunyit',
                'batch_number' => '2.1',
                'batch_type' => Expense::BATCH_TYPE_LOCAL,
                'item_code' => 'KYT-190526',
                'quantity' => 1,
                'unit_price' => 750000,
                'cost_category' => Expense::COST_CATEGORY_OPERATIONAL,
                'amount' => 750000,
            ],
            [
                'expense_date' => '2026-06-22',
                'expense_type' => 'Bongkar Muat',
                'description' => 'Bongkar 183 koli Kunyit',
                'batch_number' => '2.2',
                'batch_type' => Expense::BATCH_TYPE_LOCAL,
                'item_code' => 'KYT-220626',
                'quantity' => 183,
                'unit_price' => 20000,
                'cost_category' => Expense::COST_CATEGORY_OPERATIONAL,
                'amount' => 3660000,
            ],
            [
                'expense_date' => '2026-07-08',
                'expense_type' => 'Perlengkapan Gudang',
                'description' => 'Karung dan tali untuk Kutulak',
                'batch_number' => '3.1',
                'batch_type' => Expense::BATCH_TYPE_LOCAL,
                'item_code' => 'LAC-080726',
                'quantity' => 64,
                'unit_price' => 15000,
                'cost_category' => Expense::COST_CATEGORY_TECHNICAL,
                'amount' => 960000,
            ],
            [
                'expense_date' => '2026-04-28',
                'expense_type' => 'Administrasi Ekspor',
                'description' => 'Administrasi PEB 41.UVC.04-26',
                'batch_number' => 'E.1',
                'batch_type' => Expense::BATCH_TYPE_EXPORT,
                'item_code' => 'GBG-060426',
                'quantity' => 1,
                'unit_price' => 2500000,
                'cost_category' => Expense::COST_CATEGORY_TECHNICAL,
                'amount' => 2500000,
            ],
            [
                'expense_date' => '2026-06-25',
                'expense_type' => 'Biaya Kontainer',
                'description' => 'Sewa kontainer ekspor Kunyit',
                'batch_number' => 'E.2',
                'batch_type' => Expense::BATCH_TYPE_EXPORT,
                'item_code' => 'KYT-220626',
                'quantity' => 1,
                'unit_price' => 8000000,
                'cost_category' => Expense::COST_CATEGORY_OPERATIONAL,
                'amount' => 8000000,
            ],
            [
                'expense_date' => '2026-07-01',
                'expense_type' => 'Gaji Karyawan',
                'description' => 'Gaji karyawan periode Juli 2026',
                'batch_number' => null,
                'batch_type' => null,
                'item_code' => null,
                'quantity' => null,
                'unit_price' => null,
                'cost_category' => null,
                'amount' => 25000000,
            ],
            [
                'expense_date' => '2026-07-05',
                'expense_type' => 'Listrik dan Utilitas',
                'description' => 'Listrik gudang periode Juli 2026',
                'batch_number' => null,
                'batch_type' => null,
                'item_code' => null,
                'quantity' => null,
                'unit_price' => null,
                'cost_category' => null,
                'amount' => 3500000,
            ],
        ];
    }
}
