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
        Schema::create('material_shipment_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('material_shipment_id')->constrained('material_shipments')->cascadeOnDelete();
            $table->foreignUuid('stock_detail_id')->nullable()->constrained('stock_details')->nullOnDelete();
            $table->foreignUuid('type_id')->nullable();
            $table->foreignUuid('type_detail_id')->nullable();
            $table->char('code', 50);
            $table->char('number_serial_first', 50)->nullable();
            $table->char('number_serial_second', 50)->nullable();
            $table->decimal('quantity', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['material_shipment_id', 'stock_detail_id', 'type_id']);
            $table->index(['deleted_at', 'created_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_shipment_details');
    }
};
