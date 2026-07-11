<?php

namespace Tests\Feature;

use App\Filament\Resources\Incomes\Pages\CreateIncome;
use App\Filament\Resources\Incomes\Pages\ListIncomes;
use App\Models\Company;
use App\Models\Income;
use App\Models\IncomeType;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\IncomeTypeSeeder;
use Database\Seeders\SupplierSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IncomeManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_supplier_seeder_creates_five_suppliers_for_each_company_idempotently(): void
    {
        $this->seed(CompanySeeder::class);
        $this->seed(SupplierSeeder::class);
        $this->seed(SupplierSeeder::class);

        Company::query()->each(function (Company $company): void {
            $this->assertSame(5, Supplier::query()->whereBelongsTo($company)->count());

            foreach (['Hangtua', 'Rudy', 'Vivi Larat', 'HR', 'Alex'] as $supplierName) {
                $this->assertDatabaseHas(Supplier::class, [
                    'company_id' => $company->id,
                    'name' => $supplierName,
                ]);
            }
        });
    }

    public function test_income_type_seeder_creates_default_types_for_each_company(): void
    {
        $this->seed(CompanySeeder::class);
        $this->seed(IncomeTypeSeeder::class);

        Company::query()->each(function (Company $company): void {
            $this->assertSame(2, IncomeType::query()->whereBelongsTo($company)->count());
            $this->assertDatabaseHas(IncomeType::class, [
                'company_id' => $company->id,
                'name' => 'Penjualan',
            ]);
        });
    }

    public function test_user_can_create_income_with_thousands_separator(): void
    {
        [$company, $user] = $this->createTenantUser();
        $incomeType = IncomeType::factory()->create([
            'company_id' => $company->id,
            'name' => 'Penjualan',
        ]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateIncome::class)
            ->fillForm([
                'income_date' => '2026-07-11',
                'income_type_id' => $incomeType->id,
                'description' => 'Pembayaran penjualan kopra',
                'amount' => '7.500.000',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $income = Income::query()->where('description', 'Pembayaran penjualan kopra')->firstOrFail();

        $this->assertTrue($income->company->is($company));
        $this->assertTrue($income->incomeType->is($incomeType));
        $this->assertTrue($income->recorder->is($user));
        $this->assertSame('7500000.00', $income->amount);
    }

    public function test_user_can_create_income_type_inline_from_income_form(): void
    {
        [$company, $user] = $this->createTenantUser();

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateIncome::class)
            ->assertFormComponentActionExists('income_type_id', 'createOption')
            ->callFormComponentAction('income_type_id', 'createOption', [
                'name' => 'Pendapatan Jasa',
            ])
            ->assertHasNoFormComponentActionErrors();

        $this->assertDatabaseHas(IncomeType::class, [
            'company_id' => $company->id,
            'name' => 'Pendapatan Jasa',
        ]);
    }

    public function test_income_report_only_shows_current_company_records(): void
    {
        [$company, $user] = $this->createTenantUser();
        $otherCompany = Company::factory()->create();
        $user->companies()->attach($otherCompany);
        $visibleIncome = Income::factory()->create(['company_id' => $company->id]);
        $hiddenIncome = Income::factory()->create(['company_id' => $otherCompany->id]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(ListIncomes::class)
            ->assertCanSeeTableRecords([$visibleIncome])
            ->assertCanNotSeeTableRecords([$hiddenIncome]);
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
