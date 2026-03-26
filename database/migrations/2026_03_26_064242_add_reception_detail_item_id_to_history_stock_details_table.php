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
        Schema::table('history_stock_details', function (Blueprint $table) {
            $table->foreignUuid('reception_detail_item_id')->nullable()->after('material_usage_detail_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_stock_details', function (Blueprint $table) {
            $table->dropColumn('reception_detail_item_id');
        });
    }
};
