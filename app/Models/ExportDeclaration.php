<?php

namespace App\Models;

use Database\Factories\ExportDeclarationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

#[Fillable(['company_id', 'document_date', 'exporter_name', 'peb_number', 'invoice_number', 'container_quantity', 'container_size', 'destination_port', 'attachments', 'notes', 'recorded_by'])]
class ExportDeclaration extends Model
{
    /** @use HasFactory<ExportDeclarationFactory> */
    use HasFactory;

    protected $attributes = [
        'container_quantity' => 1,
    ];

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
        return $this->hasMany(ExportDeclarationItem::class)->orderBy('sort_order');
    }

    protected static function booted(): void
    {
        static::updated(function (ExportDeclaration $exportDeclaration): void {
            $originalAttachments = Arr::wrap($exportDeclaration->getOriginal('attachments'));
            $currentAttachments = Arr::wrap($exportDeclaration->attachments);

            Storage::disk('public')->delete(array_diff($originalAttachments, $currentAttachments));
        });

        static::deleting(function (ExportDeclaration $exportDeclaration): void {
            Storage::disk('public')->delete(Arr::wrap($exportDeclaration->attachments));
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'document_date' => 'date',
            'attachments' => 'array',
        ];
    }
}
