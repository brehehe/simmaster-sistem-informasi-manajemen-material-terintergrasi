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
        Schema::table('history_stocks', function (Blueprint $table) {
            $table->foreignUuid('reception_id')->nullable()->after('last_stock_detail_id');
            $table->foreignUuid('reception_detail_id')->nullable()->after('reception_id');
            $table->foreignUuid('material_usage_id')->nullable()->after('reception_detail_id');
            $table->foreignUuid('material_damage_id')->nullable()->after('material_usage_id');
            $table->foreignUuid('rack_assignment_id')->nullable()->after('material_damage_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_stocks', function (Blueprint $table) {
            $table->dropColumn([
                'reception_id',
                'reception_detail_id',
                'material_usage_id',
                'material_damage_id',
                'rack_assignment_id',
            ]);
        });
    }
};
