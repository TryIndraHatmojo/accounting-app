<?php

namespace App\Models;

use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'company_id',
    'expense_date',
    'expense_type_id',
    'batch_number',
    'batch_type',
    'recorded_by',
    'description',
    'item_code',
    'quantity',
    'unit_price',
    'cost_category',
    'amount',
])]
class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;

    public const BATCH_TYPE_LOCAL = 'local';

    public const BATCH_TYPE_EXPORT = 'export';

    public const COST_CATEGORY_OPERATIONAL = 'operational';

    public const COST_CATEGORY_TECHNICAL = 'technical';

    public const BATCH_TYPES = [
        self::BATCH_TYPE_LOCAL => 'Local',
        self::BATCH_TYPE_EXPORT => 'Export',
    ];

    public const COST_CATEGORIES = [
        self::COST_CATEGORY_OPERATIONAL => 'Operational Cost',
        self::COST_CATEGORY_TECHNICAL => 'Technical Cost',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'quantity' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }
}
