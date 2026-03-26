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
        Schema::create('mutation_stocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->date('mutation_date');
            $table->enum('status', ['draft', 'sent', 'received'])->default('draft');

            // Flexible sender (can be either Polda or Polres)
            $table->foreignUuid('sender_regional_police_id')->nullable()->constrained('regional_police')->cascadeOnDelete();
            $table->foreignUuid('sender_police_station_id')->nullable()->constrained('police_stations')->cascadeOnDelete();

            // Flexible receiver (can be either Polda or Polres)
            $table->foreignUuid('receiver_regional_police_id')->nullable()->constrained('regional_police')->cascadeOnDelete();
            $table->foreignUuid('receiver_police_station_id')->nullable()->constrained('police_stations')->cascadeOnDelete();

            $table->text('notes')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->foreignUuid('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['code', 'status']);
            $table->index(['sender_regional_police_id', 'sender_police_station_id']);
            $table->index(['receiver_regional_police_id', 'receiver_police_station_id']);
            $table->index(['deleted_at', 'created_at', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutation_stocks');
    }
};
