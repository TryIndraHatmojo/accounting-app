<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Expense;
use App\Models\ExportDeclaration;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ApplicationSeederTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_application_seeders_create_complete_idempotent_sample_data(): void
    {
        $this->seed();
        $this->seed();

        $companies = Company::query()
            ->withCount([
                'expenseTypes',
                'expenses',
                'exportDeclarations',
                'goodsReceipts',
                'shipmentNotices',
            ])
            ->with([
                'expenses.expenseType',
                'exportDeclarations.items',
                'goodsReceipts.items.product',
                'goodsReceipts.shipmentNotice',
                'goodsReceipts.supplier',
                'shipmentNotices.items.product',
            ])
            ->orderBy('id')
            ->get();

        $this->assertCount(2, $companies);

        foreach ($companies as $company) {
            $this->assertSame(8, $company->expense_types_count);
            $this->assertSame(8, $company->expenses_count);
            $this->assertSame(3, $company->export_declarations_count);
            $this->assertSame(7, $company->goods_receipts_count);
            $this->assertSame(7, $company->shipment_notices_count);

            $goodsReceipt = $company->goodsReceipts
                ->firstWhere('document_number', 'SBY.NNA.101');

            $this->assertNotNull($goodsReceipt);
            $this->assertSame($company->id, $goodsReceipt->shipmentNotice->company_id);
            $this->assertSame($company->id, $goodsReceipt->supplier->company_id);

            $stockItem = $goodsReceipt->items->sole();

            $this->assertSame($company->id, $stockItem->product->company_id);
            $this->assertSame('LAC-300626', $stockItem->stockCode());
            $this->assertSame(8, $stockItem->package_count);
            $this->assertSame('396.200', $stockItem->final_weight);
            $this->assertSame('1.800', $stockItem->shrinkage_weight);

            $kutulakStockItems = $company->goodsReceipts
                ->flatMap(fn (GoodsReceipt $receipt): iterable => $receipt->items)
                ->filter(fn (GoodsReceiptItem $item): bool => $item->product->name === 'Kutulak');

            $this->assertSame(80, $kutulakStockItems->sum('package_count'));
            $this->assertEquals(5434.500, $kutulakStockItems->sum('final_weight'));

            $this->assertTrue($company->exportDeclarations->every(
                fn (ExportDeclaration $exportDeclaration): bool => $exportDeclaration->items->count() === 1,
            ));
            $this->assertTrue($company->expenses->every(
                fn (Expense $expense): bool => $expense->expenseType->company_id === $company->id,
            ));
        }
    }
}
