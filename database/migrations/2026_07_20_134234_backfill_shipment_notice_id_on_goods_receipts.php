<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('goods_receipts')
            ->whereNull('shipment_notice_id')
            ->whereNotNull('origin_reference')
            ->orderBy('id')
            ->chunkById(500, function (Collection $goodsReceipts): void {
                $shipmentNotices = DB::table('shipment_notices')
                    ->whereIn('company_id', $goodsReceipts->pluck('company_id')->unique())
                    ->whereIn('document_number', $goodsReceipts->pluck('origin_reference')->filter()->unique())
                    ->get(['id', 'company_id', 'document_number'])
                    ->keyBy(fn (object $shipmentNotice): string => $shipmentNotice->company_id.'|'.$shipmentNotice->document_number);

                foreach ($goodsReceipts as $goodsReceipt) {
                    $shipmentNoticeId = $shipmentNotices
                        ->get($goodsReceipt->company_id.'|'.$goodsReceipt->origin_reference)
                        ?->id;

                    if ($shipmentNoticeId === null) {
                        continue;
                    }

                    DB::table('goods_receipts')
                        ->where('id', $goodsReceipt->id)
                        ->whereNull('shipment_notice_id')
                        ->update(['shipment_notice_id' => $shipmentNoticeId]);
                }
            });
    }

    /**
     * This data backfill cannot be safely reversed without losing pre-existing references.
     */
    public function down(): void {}
};
