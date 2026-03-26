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
        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();

            // Reference to stock_detail
            $table->foreignUuid('stock_detail_id')->nullable()->constrained('stock_details')->nullOnDelete();

            // Material info (copied for record)
            $table->foreignUuid('type_id')->constrained('types')->cascadeOnDelete();
            $table->foreignUuid('type_detail_id')->nullable()->constrained('type_details')->cascadeOnDelete();
            $table->foreignUuid('rack_id')->nullable()->constrained('racks')->nullOnDelete();
            $table->string('code', 50);
            $table->string('number_serial_first', 50)->nullable();
            $table->string('number_serial_second', 50)->nullable();

            // Quantities
            $table->decimal('system_quantity', 15, 2)->default(0); // Stock in system
            $table->decimal('physical_quantity', 15, 2)->default(0); // Actual physical count
            $table->decimal('difference', 15, 2)->default(0); // physical - system

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['deleted_at', 'created_at', 'is_active']);
            $table->index('stock_opname_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_details');
    }
};
