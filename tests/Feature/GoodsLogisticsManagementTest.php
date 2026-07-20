<?php

namespace Tests\Feature;

use App\Filament\Pages\GoodsStock;
use App\Filament\Resources\GoodsReceipts\Pages\CreateGoodsReceipt;
use App\Filament\Resources\GoodsReceipts\Pages\ListGoodsReceipts;
use App\Filament\Resources\GoodsReceipts\Pages\ViewGoodsReceipt;
use App\Filament\Resources\ShipmentNotices\Pages\CreateShipmentNotice;
use App\Filament\Resources\ShipmentNotices\Pages\ListShipmentNotices;
use App\Filament\Resources\ShipmentNotices\Pages\ViewShipmentNotice;
use App\Filament\Resources\ShipmentNotices\ShipmentNoticeResource;
use App\Models\Company;
use App\Models\ExportDeclaration;
use App\Models\ExportDeclarationItem;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\GoodsStockMovement;
use App\Models\Product;
use App\Models\ShipmentNotice;
use App\Models\Supplier;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GoodsLogisticsManagementTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_user_can_create_and_view_a_shipment_notice_based_on_ppb(): void
    {
        [$company, $user] = $this->createTenantUser();
        $product = Product::factory()->create(['company_id' => $company->id, 'name' => 'Bunga Pala HR']);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateShipmentNotice::class)
            ->fillForm([
                'document_number' => 'SINDO-06',
                'loading_date' => '2025-10-31',
                'origin' => 'Kupang, NTT',
                'departure_date' => '2025-10-31',
                'container_numbers' => "SIPU 777134 4\nSIPU 777135 3",
                'items' => [[
                    'section_name' => 'Pengiriman Express',
                    'product_id' => $product->id,
                    'package_count' => '119',
                    'initial_weight' => '7.060,00',
                    'final_weight' => '7.026,70',
                ]],
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $shipmentNotice = ShipmentNotice::query()->where('document_number', 'SINDO-06')->firstOrFail();
        $item = $shipmentNotice->items()->firstOrFail();

        $this->assertTrue($shipmentNotice->company->is($company));
        $this->assertTrue($shipmentNotice->recorder->is($user));
        $this->assertSame('7060.000', $item->initial_weight);
        $this->assertSame('7026.700', $item->final_weight);
        $this->assertSame('33.300', $item->shrinkage_weight);
        $this->assertSame('0.472', $item->shrinkage_percentage);

        Livewire::test(ViewShipmentNotice::class, ['record' => $shipmentNotice->id])
            ->assertOk();
    }

    public function test_user_can_create_supplier_inline_from_goods_receipt_form(): void
    {
        [$company, $user] = $this->createTenantUser();

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateGoodsReceipt::class)
            ->assertFormComponentActionExists('supplier_id', 'createOption')
            ->callFormComponentAction('supplier_id', 'createOption', [
                'name' => 'Supplier Baru',
            ])
            ->assertHasNoFormComponentActionErrors();

        $this->assertDatabaseHas(Supplier::class, [
            'company_id' => $company->id,
            'name' => 'Supplier Baru',
        ]);
    }

    public function test_user_can_create_and_view_a_goods_receipt_with_decimal_weight(): void
    {
        [$company, $user] = $this->createTenantUser();
        $supplier = Supplier::factory()->create(['company_id' => $company->id, 'name' => 'Hangtua']);
        $product = Product::factory()->create(['company_id' => $company->id, 'name' => 'Cengkeh BC']);
        $shipmentNotice = ShipmentNotice::factory()->create([
            'company_id' => $company->id,
            'document_number' => 'SINDO-06',
            'origin' => 'Kupang, NTT',
            'container_numbers' => 'SIPU 777134 4',
            'recorded_by' => $user->id,
        ]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(CreateGoodsReceipt::class)
            ->fillForm([
                'document_number' => 'SBY.NNA.117',
                'report_date' => '2025-11-03',
                'shipment_notice_id' => $shipmentNotice->id,
                'supplier_id' => $supplier->id,
                'origin_reference' => 'SINDO-06',
                'origin' => 'Kupang, NTT',
                'received_date' => '2025-11-14',
                'transport_type' => 'Truk',
                'container_numbers' => 'SIPU 777134 4',
                'items' => [[
                    'section_name' => 'Kontener 1 - SIPU 777134 4',
                    'product_id' => $product->id,
                    'package_count' => '3',
                    'initial_weight' => '7.060,00',
                    'final_weight' => '7.026,70',
                ]],
            ])
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $goodsReceipt = GoodsReceipt::query()->where('document_number', 'SBY.NNA.117')->firstOrFail();
        $item = $goodsReceipt->items()->firstOrFail();

        $this->assertTrue($goodsReceipt->company->is($company));
        $this->assertTrue($goodsReceipt->supplier->is($supplier));
        $this->assertTrue($goodsReceipt->shipmentNotice->is($shipmentNotice));
        $this->assertTrue($goodsReceipt->recorder->is($user));
        $this->assertSame('7060.000', $item->initial_weight);
        $this->assertSame('7026.700', $item->final_weight);

        Livewire::test(ViewGoodsReceipt::class, ['record' => $goodsReceipt->id])
            ->assertOk();
    }

    public function test_goods_receipt_ppb_reference_links_to_the_shipment_notice_view(): void
    {
        [$company, $user] = $this->createTenantUser();
        $shipmentNotice = ShipmentNotice::factory()->create(['company_id' => $company->id]);
        $goodsReceipt = GoodsReceipt::factory()->create([
            'company_id' => $company->id,
            'shipment_notice_id' => $shipmentNotice->id,
            'origin_reference' => $shipmentNotice->document_number,
        ]);

        $this->actingAs($user);
        $this->setTenant($company);

        $shipmentNoticeViewUrl = ShipmentNoticeResource::getUrl('view', [
            'record' => $shipmentNotice,
        ]);

        Livewire::test(ListGoodsReceipts::class)
            ->assertTableColumnExists(
                'shipmentNotice.document_number',
                fn (TextColumn $column): bool => $column->getUrl() === $shipmentNoticeViewUrl,
                $goodsReceipt,
            );

        Livewire::test(ViewGoodsReceipt::class, ['record' => $goodsReceipt->id])
            ->assertSchemaComponentExists(
                'shipmentNotice.document_number',
                'infolist',
                fn (TextEntry $entry): bool => $entry->getUrl() === $shipmentNoticeViewUrl,
            );
    }

    public function test_logistics_documents_are_isolated_by_company(): void
    {
        [$company, $user] = $this->createTenantUser();
        $otherCompany = Company::factory()->create();
        $user->companies()->attach($otherCompany);
        $visibleShipment = ShipmentNotice::factory()->create(['company_id' => $company->id]);
        $hiddenShipment = ShipmentNotice::factory()->create(['company_id' => $otherCompany->id]);
        $visibleReceipt = GoodsReceipt::factory()->create(['company_id' => $company->id]);
        $hiddenReceipt = GoodsReceipt::factory()->create(['company_id' => $otherCompany->id]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(ListShipmentNotices::class)
            ->assertCanSeeTableRecords([$visibleShipment])
            ->assertCanNotSeeTableRecords([$hiddenShipment]);

        Livewire::test(ListGoodsReceipts::class)
            ->assertCanSeeTableRecords([$visibleReceipt])
            ->assertCanNotSeeTableRecords([$hiddenReceipt]);
    }

    public function test_goods_stock_uses_product_abbreviation_and_received_date_per_company(): void
    {
        [$company, $user] = $this->createTenantUser();
        $otherCompany = Company::factory()->create();
        $product = Product::factory()->create([
            'company_id' => $company->id,
            'name' => 'Kutulak',
            'abbreviation' => 'lac',
        ]);
        $otherProduct = Product::factory()->create([
            'company_id' => $otherCompany->id,
            'name' => 'Biji Gebang',
            'abbreviation' => 'gbg',
        ]);
        $supplier = Supplier::factory()->create(['company_id' => $company->id]);
        $otherSupplier = Supplier::factory()->create(['company_id' => $otherCompany->id]);
        $goodsReceipt = GoodsReceipt::factory()->create([
            'company_id' => $company->id,
            'shipment_notice_id' => null,
            'supplier_id' => $supplier->id,
            'report_date' => '2026-06-29',
            'received_date' => '2026-06-30',
            'recorded_by' => $user->id,
        ]);
        $otherGoodsReceipt = GoodsReceipt::factory()->create([
            'company_id' => $otherCompany->id,
            'shipment_notice_id' => null,
            'supplier_id' => $otherSupplier->id,
            'received_date' => '2026-04-06',
        ]);
        $visibleItem = GoodsReceiptItem::factory()->create([
            'goods_receipt_id' => $goodsReceipt->id,
            'product_id' => $product->id,
            'package_count' => 64,
            'initial_weight' => 4800,
            'final_weight' => 4736,
        ]);
        $hiddenItem = GoodsReceiptItem::factory()->create([
            'goods_receipt_id' => $otherGoodsReceipt->id,
            'product_id' => $otherProduct->id,
            'package_count' => 3,
            'initial_weight' => 200,
            'final_weight' => 185,
        ]);
        $exportDeclaration = ExportDeclaration::factory()->create([
            'company_id' => $company->id,
            'document_date' => '2026-07-10',
            'peb_number' => '43.UVC.07-26',
            'recorded_by' => $user->id,
        ]);
        $exportItem = ExportDeclarationItem::factory()->create([
            'export_declaration_id' => $exportDeclaration->id,
            'product_id' => $product->id,
            'description' => 'Kutulak',
            'bag_count' => 10,
            'net_weight' => 680,
        ]);
        $outgoingMovement = (new GoodsStockMovement)->forceFill([
            'id' => -$exportItem->id,
        ]);

        $this->actingAs($user);
        $this->setTenant($company);

        Livewire::test(GoodsStock::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$visibleItem, $outgoingMovement])
            ->assertCanNotSeeTableRecords([$hiddenItem])
            ->assertTableColumnStateSet('stock_code', 'LAC-300626', $visibleItem)
            ->assertTableColumnStateSet('package_change', 64, $visibleItem)
            ->assertTableColumnStateSet('weight_change', '4736.000', $visibleItem)
            ->assertTableColumnStateSet('movement_type', GoodsStockMovement::TYPE_OUTGOING, $outgoingMovement)
            ->assertTableColumnStateSet('reference_number', '43.UVC.07-26', $outgoingMovement)
            ->assertTableColumnStateSet('package_change', -10, $outgoingMovement)
            ->assertTableColumnStateSet('weight_change', '-680.000', $outgoingMovement)
            ->assertTableColumnSummarySet('package_change', 'stock', 54)
            ->assertTableColumnSummarySet('weight_change', 'stock', 4056);
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
