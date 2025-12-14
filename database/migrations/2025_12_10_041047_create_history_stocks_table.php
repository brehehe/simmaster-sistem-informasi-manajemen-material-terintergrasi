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
        Schema::create('history_stocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('code',50)->unique();
            $table->foreignUuid('last_stock_id')->nullable();
            $table->foreignUuid('last_stock_detail_id')->nullable();
            $table->foreignUuid('type_id')->nullable();
            $table->foreignUuid('type_detail_id')->nullable();
            $table->foreignUuid('regional_police_id')->nullable();
            $table->foreignUuid('police_station_id')->nullable();
            $table->foreignUuid('rack_id')->nullable();
            $table->date('date');
            $table->longText('serial_number')->nullable();
            $table->enum('status_type',['in','first','last','out'])->default('in');
            $table->decimal('quantity')->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['deleted_at','created_at','is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_stocks');
    }
};
