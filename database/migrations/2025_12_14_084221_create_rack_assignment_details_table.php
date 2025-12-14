<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rack_assignment_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('rack_assignment_id');
            $table->uuid('stock_detail_id')->nullable();
            $table->uuid('type_id')->nullable();
            $table->uuid('type_detail_id')->nullable();
            $table->uuid('from_rack_id')->nullable();
            $table->uuid('to_rack_id');
            $table->string('item_code')->nullable();
            $table->string('number_serial_first')->nullable();
            $table->string('number_serial_second')->nullable();
            $table->decimal('quantity', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('rack_assignment_id')->references('id')->on('rack_assignments')->onDelete('cascade');
            $table->foreign('stock_detail_id')->references('id')->on('stock_details')->onDelete('set null');
            $table->foreign('type_id')->references('id')->on('types')->onDelete('set null');
            $table->foreign('type_detail_id')->references('id')->on('type_details')->onDelete('set null');
            $table->foreign('from_rack_id')->references('id')->on('racks')->onDelete('set null');
            $table->foreign('to_rack_id')->references('id')->on('racks')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rack_assignment_details');
    }
};
