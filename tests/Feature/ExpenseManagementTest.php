<?php

namespace Tests\Feature;

use App\Filament\Resources\Expenses\Pages\CreateExpense;
use App\Filament\Resources\Expenses\Pages\ListExpenses;
use App\Models\Company;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\ExpenseTypeSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExpenseManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_expense_type_seeder_creates_default_types_idempotently(): void
    {
        $this->seed(CompanySeeder::class);
        $this->seed(ExpenseTypeSeeder::class);
        $this->seed(ExpenseTypeSeeder::class);

        $this->assertSame(16, ExpenseType::query()->count());

        Company::query()->each(function (Company $company): void {
            $this->assertDatabaseHas(ExpenseType::class, [
                'company_id' => $company->id,
                'name' => 'Gaji Karyawan',
            ]);
            $this->assertDatabaseHas(ExpenseType::class, [
                'company_id' => $company->id,
                'name' => 'Pengeluaran Perusahaan Lainnya',
            ]);
            $this->assertDatabaseHas(ExpenseType::class, [
                'company_id' => $company->id,
                'name' => 'Administrasi Ekspor',
            ]);
        });
    }

    public function test_user_can_create_an_expense_from_filament(): void
    {
        [$company, $user] = $this->createTenantUser();
        $expenseType = ExpenseType::factory()->create([
            'company_id' => $company->id,
            'name' => 'Gaji Karyawan',
        ]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateExpense::class)
            ->fillForm([
                'expense_date' => '2026-07-01',
                'expense_type_id' => $expenseType->id,
                'description' => 'Gaji Budi periode Juli 2026',
                'amount' => '7.500.000',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $expense = Expense::query()->where('description', 'Gaji Budi periode Juli 2026')->firstOrFail();

        $this->assertTrue($expense->company->is($company));
        $this->assertTrue($expense->expenseType->is($expenseType));
        $this->assertTrue($expense->recorder->is($user));
        $this->assertSame('7500000.00', $expense->amount);
    }

    public function test_user_can_create_an_expense_type_inline_from_expense_form(): void
    {
        [$company, $user] = $this->createTenantUser();

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateExpense::class)
            ->assertFormComponentActionExists('expense_type_id', 'createOption')
            ->callFormComponentAction('expense_type_id', 'createOption', [
                'name' => 'Biaya Dokumen Ekspor',
            ])
            ->assertHasNoFormComponentActionErrors();

        $this->assertDatabaseHas(ExpenseType::class, ['name' => 'Biaya Dokumen Ekspor']);
    }

    public function test_user_can_create_a_batch_expense_with_calculated_technical_cost(): void
    {
        [$company, $user] = $this->createTenantUser();
        $expenseType = ExpenseType::factory()->create(['company_id' => $company->id]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateExpense::class)
            ->fillForm([
                'expense_date' => '2025-11-27',
                'expense_type_id' => $expenseType->id,
                'batch_number' => '5.2',
                'batch_type' => 'export',
                'description' => 'Bongkar 4x20ft SINDO',
                'item_code' => 'MT-271125',
                'quantity' => 593,
                'unit_price' => '1.602',
                'cost_category' => 'technical',
                'amount' => 1,
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $expense = Expense::query()->where('item_code', 'MT-271125')->firstOrFail();

        $this->assertSame('5.2', $expense->batch_number);
        $this->assertSame('export', $expense->batch_type);
        $this->assertSame('technical', $expense->cost_category);
        $this->assertSame('593.000', $expense->quantity);
        $this->assertSame('1602.00', $expense->unit_price);
        $this->assertSame('949986.00', $expense->amount);
    }

    public function test_user_can_view_expenses_in_the_report_table(): void
    {
        [$company, $user] = $this->createTenantUser();
        $expenseType = ExpenseType::factory()->create(['company_id' => $company->id]);
        $expenses = Expense::factory()
            ->count(2)
            ->recycle([$user, $expenseType])
            ->create(['company_id' => $company->id]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(ListExpenses::class)
            ->assertCanSeeTableRecords($expenses)
            ->assertTableColumnExists('operational_cost')
            ->assertTableColumnExists('technical_cost');
    }

    public function test_expense_form_rejects_an_invalid_amount(): void
    {
        [$company, $user] = $this->createTenantUser();

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateExpense::class)
            ->fillForm([
                'expense_date' => '2026-07-01',
                'expense_type_id' => ExpenseType::factory()->create(['company_id' => $company->id])->id,
                'description' => 'Nominal tidak valid',
                'amount' => 0,
            ])
            ->call('create')
            ->assertHasFormErrors(['amount' => 'min']);
    }

    public function test_expense_form_requires_complete_batch_cost_details(): void
    {
        [$company, $user] = $this->createTenantUser();

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateExpense::class)
            ->fillForm([
                'expense_date' => '2025-11-27',
                'expense_type_id' => ExpenseType::factory()->create(['company_id' => $company->id])->id,
                'description' => 'Packing Mente',
                'quantity' => 150,
                'cost_category' => 'technical',
                'amount' => 900000,
            ])
            ->call('create')
            ->assertHasFormErrors(['unit_price' => 'required_with']);
    }

    /**
     * @return array{Company, User}
     */
    private function createTenantUser(): array
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $user->companies()->attach($company);

        return [$company, $user];
    }

    private function setTenant(Company $company): void
    {
        Filament::setTenant($company);
        Filament::bootCurrentPanel();
    }
}
