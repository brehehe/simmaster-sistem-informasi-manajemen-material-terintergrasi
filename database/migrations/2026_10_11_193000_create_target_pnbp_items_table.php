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
        Schema::create('target_pnbp_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('target_id')->nullable()->constrained('targets')->onDelete('cascade');
            $table->integer('year')->index();
            $table->string('sheet_name', 30)->index();
            $table->integer('row_index')->default(0);
            $table->string('no', 30)->nullable();
            $table->string('name');
            $table->smallInteger('level')->default(2); // 1: Header/Kategori, 2: Sub, 3: Rincian, 0: Total
            $table->decimal('tarif', 16, 2)->default(0);
            $table->decimal('volume_target', 16, 2)->default(0);
            $table->decimal('target_rp', 16, 2)->default(0);
            $table->decimal('volume_realization', 16, 2)->default(0);
            $table->decimal('realization_rp', 16, 2)->default(0);
            $table->decimal('percentage', 8, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['year', 'sheet_name']);
            $table->index(['target_id', 'sheet_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_pnbp_items');
    }
};
