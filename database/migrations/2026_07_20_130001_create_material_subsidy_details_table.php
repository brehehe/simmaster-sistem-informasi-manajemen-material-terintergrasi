<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_subsidy_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('material_subsidy_id')->constrained('material_subsidies')->cascadeOnDelete();
            $table->foreignUuid('type_id')->constrained('types')->cascadeOnDelete();
            $table->foreignUuid('type_detail_id')->nullable()->constrained('type_details')->nullOnDelete();
            $table->foreignUuid('stock_detail_id')->nullable()->constrained('stock_details')->nullOnDelete();
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['material_subsidy_id', 'type_id', 'type_detail_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_subsidy_details');
    }
};
