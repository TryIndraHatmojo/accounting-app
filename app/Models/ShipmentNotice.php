<?php

namespace App\Models;

use Database\Factories\ShipmentNoticeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'document_number', 'loading_date', 'origin', 'departure_date', 'vehicle_number', 'container_numbers', 'seal_numbers', 'notes', 'recorded_by'])]
class ShipmentNotice extends Model
{
    /** @use HasFactory<ShipmentNoticeFactory> */
    use HasFactory;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShipmentNoticeItem::class)->orderBy('sort_order');
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'loading_date' => 'date',
            'departure_date' => 'date',
        ];
    }
}
