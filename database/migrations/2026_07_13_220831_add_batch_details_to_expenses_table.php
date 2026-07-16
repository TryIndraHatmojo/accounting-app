<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('batch_number')->nullable()->after('expense_type_id');
            $table->string('batch_type')->nullable()->after('batch_number');
            $table->string('item_code')->nullable()->after('description');
            $table->decimal('quantity', 18, 3)->nullable()->after('item_code');
            $table->decimal('unit_price', 18, 2)->nullable()->after('quantity');
            $table->string('cost_category')->nullable()->after('unit_price');

            $table->index(['company_id', 'batch_number']);
            $table->index(['company_id', 'batch_type', 'cost_category'], 'expenses_company_batch_cost_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'batch_number']);
            $table->dropIndex('expenses_company_batch_cost_index');
            $table->dropColumn([
                'batch_number',
                'batch_type',
                'item_code',
                'quantity',
                'unit_price',
                'cost_category',
            ]);
        });
    }
};
