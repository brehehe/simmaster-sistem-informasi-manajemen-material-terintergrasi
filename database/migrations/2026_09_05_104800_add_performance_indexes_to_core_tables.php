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
            $table->index(['date', 'status_type'], 'idx_history_stocks_date_status');
            $table->index(['regional_police_id', 'date'], 'idx_history_stocks_regional_date');
            $table->index(['police_station_id', 'date'], 'idx_history_stocks_polres_date');
            $table->index('type_id', 'idx_history_stocks_type');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->index(['regional_police_id', 'type_id', 'type_detail_id'], 'idx_stocks_regional_type_detail');
            $table->index(['police_station_id', 'type_id', 'type_detail_id'], 'idx_stocks_polres_type_detail');
        });

        Schema::table('stock_details', function (Blueprint $table) {
            $table->index(['stock_id', 'type_id'], 'idx_stock_details_stock_type');
            $table->index(['regional_police_id', 'type_id'], 'idx_stock_details_reg_type');
            $table->index(['police_station_id', 'type_id'], 'idx_stock_details_pol_type');
        });

        Schema::table('material_usages', function (Blueprint $table) {
            $table->index(['date', 'regional_police_id'], 'idx_mat_usages_date_regional');
            $table->index(['date', 'police_station_id'], 'idx_mat_usages_date_polres');
        });

        Schema::table('material_usage_details', function (Blueprint $table) {
            $table->index(['material_usage_id', 'type_id'], 'idx_mud_usage_type');
            $table->index('type_id', 'idx_mud_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_usage_details', function (Blueprint $table) {
            $table->dropIndex('idx_mud_usage_type');
            $table->dropIndex('idx_mud_type');
        });

        Schema::table('material_usages', function (Blueprint $table) {
            $table->dropIndex('idx_mat_usages_date_regional');
            $table->dropIndex('idx_mat_usages_date_polres');
        });

        Schema::table('stock_details', function (Blueprint $table) {
            $table->dropIndex('idx_stock_details_stock_type');
            $table->dropIndex('idx_stock_details_reg_type');
            $table->dropIndex('idx_stock_details_pol_type');
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropIndex('idx_stocks_regional_type_detail');
            $table->dropIndex('idx_stocks_polres_type_detail');
        });

        Schema::table('history_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_history_stocks_date_status');
            $table->dropIndex('idx_history_stocks_regional_date');
            $table->dropIndex('idx_history_stocks_polres_date');
            $table->dropIndex('idx_history_stocks_type');
        });
    }
};
