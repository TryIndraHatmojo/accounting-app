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
        Schema::create('shipment_notice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_notice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('section_name')->nullable();
            $table->unsignedInteger('package_count')->nullable();
            $table->decimal('initial_weight', 18, 3)->nullable();
            $table->decimal('final_weight', 18, 3)->nullable();
            $table->decimal('shrinkage_weight', 18, 3)->nullable();
            $table->decimal('shrinkage_percentage', 8, 3)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['shipment_notice_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_notice_items');
    }
};
