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
        Schema::table('expense_types', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->foreignId('company_id')
                ->nullable()
                ->after('id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unique(['company_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_types', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'name']);
            $table->dropConstrainedForeignId('company_id');
            $table->unique('name');
        });
    }
};
