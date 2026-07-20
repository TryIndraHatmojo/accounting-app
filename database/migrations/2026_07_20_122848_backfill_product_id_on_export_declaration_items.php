<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $productIds = DB::table('products')
            ->select(['id', 'company_id', 'name'])
            ->get()
            ->mapWithKeys(fn (object $product): array => [
                $product->company_id.'|'.Str::lower(Str::squish($product->name)) => $product->id,
            ]);

        DB::table('export_declaration_items')
            ->join(
                'export_declarations',
                'export_declarations.id',
                '=',
                'export_declaration_items.export_declaration_id',
            )
            ->whereNull('export_declaration_items.product_id')
            ->select([
                'export_declaration_items.id',
                'export_declaration_items.description',
                'export_declarations.company_id',
            ])
            ->orderBy('export_declaration_items.id')
            ->each(function (object $item) use ($productIds): void {
                $productId = $productIds->get(
                    $item->company_id.'|'.Str::lower(Str::squish($item->description)),
                );

                if ($productId === null) {
                    return;
                }

                DB::table('export_declaration_items')
                    ->where('id', $item->id)
                    ->update(['product_id' => $productId]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The matched product is valid domain data and should not be erased on rollback.
    }
};
