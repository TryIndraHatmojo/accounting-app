<?php

namespace App\Models;

use Database\Factories\ExportDeclarationItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['export_declaration_id', 'container_number', 'seal_number', 'warehouse', 'container_size', 'description', 'gross_weight', 'net_weight', 'bag_count', 'sort_order'])]
class ExportDeclarationItem extends Model
{
    /** @use HasFactory<ExportDeclarationItemFactory> */
    use HasFactory;

    public function exportDeclaration(): BelongsTo
    {
        return $this->belongsTo(ExportDeclaration::class);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'gross_weight' => 'decimal:3',
            'net_weight' => 'decimal:3',
        ];
    }
}
