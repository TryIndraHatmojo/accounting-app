<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsStockMovement extends Model
{
    public const TYPE_INCOMING = 'incoming';

    public const TYPE_OUTGOING = 'outgoing';

    public $timestamps = false;

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockCode(): string
    {
        if (blank($this->product->abbreviation) || $this->movement_date === null) {
            return '-';
        }

        return $this->product->abbreviation.'-'.$this->movement_date->format('dmy');
    }

    public function isOutgoing(): bool
    {
        return $this->movement_type === self::TYPE_OUTGOING;
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'movement_date' => 'date',
            'package_change' => 'integer',
            'weight_change' => 'decimal:3',
            'reference_id' => 'integer',
        ];
    }
}
