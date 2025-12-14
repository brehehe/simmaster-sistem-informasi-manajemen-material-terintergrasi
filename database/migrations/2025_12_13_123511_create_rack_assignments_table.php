<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rack_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->foreignUuid('stock_detail_id');
            $table->foreignUuid('type_id')->nullable();
            $table->foreignUuid('type_detail_id')->nullable();
            $table->foreignUuid('from_rack_id')->nullable();
            $table->foreignUuid('to_rack_id');
            $table->string('item_code')->nullable();
            $table->string('number_serial_first')->nullable();
            $table->string('number_serial_second')->nullable();
            $table->decimal('quantity')->default(0);
            $table->foreignUuid('regional_police_id')->nullable();
            $table->foreignUuid('police_station_id')->nullable();
            $table->date('date');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            $table->index(['deleted_at', 'created_at', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rack_assignments');
    }
};
