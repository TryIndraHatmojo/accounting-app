<?php

namespace App\Models;

use Database\Factories\IncomeTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'name'])]
class IncomeType extends Model
{
    /** @use HasFactory<IncomeTypeFactory> */
    use HasFactory;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
}
