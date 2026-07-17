<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Company;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\CompanySeeder;
use Database\Seeders\ProductSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductCompanyTenancyTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_product_seeder_creates_ten_products_for_each_company_idempotently(): void
    {
        $this->seed(CompanySeeder::class);
        $this->seed(ProductSeeder::class);
        $this->seed(ProductSeeder::class);

        Company::query()->each(function (Company $company): void {
            $this->assertSame(10, Product::query()->whereBelongsTo($company)->count());
            $this->assertDatabaseHas(Product::class, [
                'company_id' => $company->id,
                'name' => 'Kopra',
            ]);
            $this->assertDatabaseHas(Product::class, [
                'company_id' => $company->id,
                'name' => 'Bunga Pala',
                'abbreviation' => 'BPA',
            ]);
        });
    }

    public function test_user_must_select_an_accessible_company_when_logging_in(): void
    {
        $accessibleCompany = Company::factory()->create();
        $inaccessibleCompany = Company::factory()->create();
        $user = User::factory()->create(['password' => 'password']);
        $user->companies()->attach($accessibleCompany);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => 'password',
                'company_id' => $inaccessibleCompany->id,
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['company_id']);

        $this->assertGuest();

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => 'password',
                'company_id' => $accessibleCompany->id,
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($user);
        $this->assertSame($accessibleCompany->id, session('selected_company_id'));
    }

    public function test_product_resource_only_shows_products_from_current_company(): void
    {
        $company = Company::factory()->create();
        $otherCompany = Company::factory()->create();
        $user = User::factory()->create();
        $user->companies()->attach([$company->id, $otherCompany->id]);
        $visibleProduct = Product::factory()->create(['company_id' => $company->id]);
        $hiddenProduct = Product::factory()->create(['company_id' => $otherCompany->id]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(ListProducts::class)
            ->assertCanSeeTableRecords([$visibleProduct])
            ->assertCanNotSeeTableRecords([$hiddenProduct]);
    }

    public function test_created_product_is_automatically_owned_by_current_company(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create();
        $user->companies()->attach($company);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Vanili',
                'abbreviation' => 'vnl',
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $this->assertDatabaseHas(Product::class, [
            'company_id' => $company->id,
            'name' => 'Vanili',
            'abbreviation' => 'VNL',
        ]);
    }

    private function setTenant(Company $company): void
    {
        Filament::setTenant($company);
        Filament::bootCurrentPanel();
    }
}
