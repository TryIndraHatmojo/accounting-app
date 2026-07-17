<?php

namespace App\Models;

use Database\Factories\GoodsReceiptItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['goods_receipt_id', 'product_id', 'section_name', 'package_count', 'initial_weight', 'final_weight', 'shrinkage_weight', 'shrinkage_percentage', 'sort_order'])]
class GoodsReceiptItem extends Model
{
    /** @use HasFactory<GoodsReceiptItemFactory> */
    use HasFactory;

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockCode(): string
    {
        if (blank($this->product->abbreviation) || $this->goodsReceipt->received_date === null) {
            return '-';
        }

        return $this->product->abbreviation.'-'.$this->goodsReceipt->received_date->format('dmy');
    }

    protected static function booted(): void
    {
        static::saving(function (GoodsReceiptItem $item): void {
            $item->calculateShrinkage();
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'initial_weight' => 'decimal:3',
            'final_weight' => 'decimal:3',
            'shrinkage_weight' => 'decimal:3',
            'shrinkage_percentage' => 'decimal:3',
        ];
    }

    private function calculateShrinkage(): void
    {
        if ($this->initial_weight === null || $this->final_weight === null) {
            $this->shrinkage_weight = null;
            $this->shrinkage_percentage = null;

            return;
        }

        $initialWeight = (float) $this->initial_weight;
        $shrinkageWeight = $initialWeight - (float) $this->final_weight;

        $this->shrinkage_weight = round($shrinkageWeight, 3);
        $this->shrinkage_percentage = $initialWeight > 0
            ? round(($shrinkageWeight / $initialWeight) * 100, 3)
            : null;
    }
}
