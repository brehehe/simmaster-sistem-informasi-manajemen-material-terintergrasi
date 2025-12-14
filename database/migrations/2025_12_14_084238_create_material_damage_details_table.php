<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_damage_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('material_damage_id');
            $table->uuid('stock_detail_id')->nullable();
            $table->uuid('type_id')->nullable();
            $table->uuid('type_detail_id')->nullable();
            $table->uuid('rack_id')->nullable();
            $table->string('item_code')->nullable();
            $table->string('number_serial_first')->nullable();
            $table->string('number_serial_second')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->enum('damage_type', ['damaged', 'lost']);
            $table->text('reason');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('material_damage_id')->references('id')->on('material_damages')->onDelete('cascade');
            $table->foreign('stock_detail_id')->references('id')->on('stock_details')->onDelete('set null');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('set null');
            $table->foreign('type_detail_id')->references('id')->on('type_details')->onDelete('set null');
            $table->foreign('rack_id')->references('id')->on('racks')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_damage_details');
    }
};
