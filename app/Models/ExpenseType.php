<?php

namespace App\Models;

use Database\Factories\ExpenseTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class ExpenseType extends Model
{
    /** @use HasFactory<ExpenseTypeFactory> */
    use HasFactory;

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
}
