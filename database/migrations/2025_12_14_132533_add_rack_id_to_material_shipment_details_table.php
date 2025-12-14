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
        Schema::table('material_shipment_details', function (Blueprint $table) {
            $table->uuid('rack_id')->nullable()->after('stock_detail_id');
            $table->foreign('rack_id')->references('id')->on('racks')->onDelete('set null');

            $table->index('rack_id', 'idx_material_shipment_details_rack');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_shipment_details', function (Blueprint $table) {
            $table->dropForeign(['rack_id']);
            $table->dropIndex('idx_material_shipment_details_rack');
            $table->dropColumn('rack_id');
        });
    }
};
