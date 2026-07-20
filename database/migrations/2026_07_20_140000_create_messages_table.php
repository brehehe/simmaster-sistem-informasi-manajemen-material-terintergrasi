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
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->foreignUuid('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignUuid('sender_regional_police_id')->nullable()->constrained('regional_police')->onDelete('set null');
            $table->foreignUuid('sender_police_station_id')->nullable()->constrained('police_stations')->onDelete('set null');
            
            $table->enum('receiver_type', ['all', 'polda', 'polres'])->default('all');
            $table->foreignUuid('receiver_regional_police_id')->nullable()->constrained('regional_police')->onDelete('set null');
            $table->foreignUuid('receiver_police_station_id')->nullable()->constrained('police_stations')->onDelete('set null');
            
            $table->enum('category', ['material_damage', 'cross_subsidy', 'general_info'])->default('general_info');
            $table->string('subject');
            $table->text('message');
            $table->string('attachment_path')->nullable();
            
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category', 'receiver_type', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
