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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->date('opname_date');

            // Owner (Polda OR Polres, not both)
            $table->foreignUuid('regional_police_id')->nullable()->constrained('regional_police')->nullOnDelete();
            $table->foreignUuid('police_station_id')->nullable()->constrained('police_stations')->nullOnDelete();

            // Status workflow
            $table->enum('status', ['draft', 'completed', 'approved'])->default('draft');

            // Notes and metadata
            $table->text('notes')->nullable();

            // Audit trail
            $table->foreignUuid('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['deleted_at', 'created_at', 'is_active']);
            $table->index('opname_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
