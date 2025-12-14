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
        Schema::create('reception_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reception_id');
            $table->foreignUuid('type_id')->nullable();
            $table->foreignUuid('type_detail_id')->nullable();
            $table->foreignUuid('rack_id')->nullable();
            $table->char('code')->nullable();
            $table->string('number_serial_first')->nullable();
            $table->string('number_serial_second')->nullable();
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
        Schema::dropIfExists('reception_details');
    }
};
