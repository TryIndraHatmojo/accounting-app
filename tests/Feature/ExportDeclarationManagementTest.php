<?php

namespace Tests\Feature;

use App\Filament\Resources\ExportDeclarations\Pages\CreateExportDeclaration;
use App\Filament\Resources\ExportDeclarations\Pages\ListExportDeclarations;
use App\Filament\Resources\ExportDeclarations\Pages\ViewExportDeclaration;
use App\Models\Company;
use App\Models\ExportDeclaration;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ExportDeclarationManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_user_can_create_a_peb_with_multiple_optimized_attachments(): void
    {
        Storage::fake('public');
        [$company, $user] = $this->createTenantUser();

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateExportDeclaration::class)
            ->fillForm([
                'document_date' => '2026-06-29',
                'exporter_name' => 'CV. UNIVERSAL VINDO COCO',
                'peb_number' => '41.UVC.06-26',
                'invoice_number' => '032/LAC/RSS-INV/05/26',
                'container_quantity' => 1,
                'container_size' => '20ft',
                'destination_port' => 'India',
                'items' => [[
                    'container_number' => 'SIKU 307838 5',
                    'seal_number' => '0087588',
                    'warehouse' => 'UVC00059',
                    'container_size' => '20ft',
                    'description' => 'Sticklac',
                    'gross_weight' => '14.740,0',
                    'net_weight' => '14.690,0',
                    'bag_count' => 297,
                ]],
                'attachments' => [
                    UploadedFile::fake()->image('kondisi-karung.jpg', 2600, 1800),
                    UploadedFile::fake()->image('label-sticklac.png', 1200, 1600),
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $exportDeclaration = ExportDeclaration::query()->where('peb_number', '41.UVC.06-26')->firstOrFail();
        $item = $exportDeclaration->items()->firstOrFail();

        $this->assertTrue($exportDeclaration->company->is($company));
        $this->assertTrue($exportDeclaration->recorder->is($user));
        $this->assertSame('14740.000', $item->gross_weight);
        $this->assertSame('14690.000', $item->net_weight);
        $this->assertCount(2, $exportDeclaration->attachments);

        foreach ($exportDeclaration->attachments as $attachment) {
            Storage::disk('public')->assertExists($attachment);
            $this->assertStringEndsWith('.webp', $attachment);

            $dimensions = getimagesize(Storage::disk('public')->path($attachment));

            $this->assertIsArray($dimensions);
            $this->assertLessThanOrEqual(2000, max($dimensions[0], $dimensions[1]));
        }

        Livewire::test(ViewExportDeclaration::class, ['record' => $exportDeclaration->id])
            ->assertOk();
    }

    public function test_removed_and_deleted_attachments_are_deleted_from_storage(): void
    {
        Storage::fake('public');
        $exportDeclaration = ExportDeclaration::factory()->create([
            'attachments' => [
                'export-declarations/1/dihapus.webp',
                'export-declarations/1/dipertahankan.webp',
            ],
        ]);

        Storage::disk('public')->put('export-declarations/1/dihapus.webp', 'old-image');
        Storage::disk('public')->put('export-declarations/1/dipertahankan.webp', 'kept-image');

        $exportDeclaration->update([
            'attachments' => ['export-declarations/1/dipertahankan.webp'],
        ]);

        Storage::disk('public')->assertMissing('export-declarations/1/dihapus.webp');
        Storage::disk('public')->assertExists('export-declarations/1/dipertahankan.webp');

        $exportDeclaration->delete();

        Storage::disk('public')->assertMissing('export-declarations/1/dipertahankan.webp');
    }

    public function test_export_declarations_are_isolated_by_company(): void
    {
        [$company, $user] = $this->createTenantUser();
        $otherCompany = Company::factory()->create();
        $user->companies()->attach($otherCompany);
        $visibleDeclaration = ExportDeclaration::factory()->create(['company_id' => $company->id]);
        $hiddenDeclaration = ExportDeclaration::factory()->create(['company_id' => $otherCompany->id]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(ListExportDeclarations::class)
            ->assertCanSeeTableRecords([$visibleDeclaration])
            ->assertCanNotSeeTableRecords([$hiddenDeclaration]);
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
