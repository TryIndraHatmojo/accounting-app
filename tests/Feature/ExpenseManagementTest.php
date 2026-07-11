<?php

namespace Tests\Feature;

use App\Filament\Resources\Expenses\Pages\CreateExpense;
use App\Filament\Resources\Expenses\Pages\ListExpenses;
use App\Models\Expense;
use App\Models\ExpenseType;
use App\Models\User;
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
        $this->seed(ExpenseTypeSeeder::class);
        $this->seed(ExpenseTypeSeeder::class);

        $this->assertSame(2, ExpenseType::query()->count());
        $this->assertDatabaseHas(ExpenseType::class, ['name' => 'Gaji Karyawan']);
        $this->assertDatabaseHas(ExpenseType::class, ['name' => 'Pengeluaran Perusahaan Lainnya']);
    }

    public function test_user_can_create_an_expense_from_filament(): void
    {
        $user = User::factory()->create();
        $expenseType = ExpenseType::factory()->create(['name' => 'Gaji Karyawan']);

        $this->actingAs($user);

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

        $this->assertTrue($expense->expenseType->is($expenseType));
        $this->assertTrue($expense->recorder->is($user));
        $this->assertSame('7500000.00', $expense->amount);
    }

    public function test_user_can_create_an_expense_type_inline_from_expense_form(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateExpense::class)
            ->assertFormComponentActionExists('expense_type_id', 'createOption')
            ->callFormComponentAction('expense_type_id', 'createOption', [
                'name' => 'Biaya Dokumen Ekspor',
            ])
            ->assertHasNoFormComponentActionErrors();

        $this->assertDatabaseHas(ExpenseType::class, ['name' => 'Biaya Dokumen Ekspor']);
    }

    public function test_user_can_view_expenses_in_the_report_table(): void
    {
        $user = User::factory()->create();
        $expenseType = ExpenseType::factory()->create();
        $expenses = Expense::factory()
            ->count(2)
            ->recycle([$user, $expenseType])
            ->create();

        $this->actingAs($user);

        Livewire::test(ListExpenses::class)
            ->assertCanSeeTableRecords($expenses);
    }

    public function test_expense_form_rejects_an_invalid_amount(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreateExpense::class)
            ->fillForm([
                'expense_date' => '2026-07-01',
                'expense_type_id' => ExpenseType::factory()->create()->id,
                'description' => 'Nominal tidak valid',
                'amount' => 0,
            ])
            ->call('create')
            ->assertHasFormErrors(['amount' => 'min']);
    }
}
