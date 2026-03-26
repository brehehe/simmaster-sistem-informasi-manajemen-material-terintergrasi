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
        Schema::create('material_shipments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->date('shipment_date');
            $table->enum('status', ['draft', 'shipped', 'received'])->default('draft');
            $table->foreignUuid('sender_regional_police_id')->constrained('regional_police')->cascadeOnDelete();
            $table->foreignUuid('receiver_police_station_id')->constrained('police_stations')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->foreignUuid('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['code', 'status', 'sender_regional_police_id', 'receiver_police_station_id']);
            $table->index(['deleted_at', 'created_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_shipments');
    }
};
