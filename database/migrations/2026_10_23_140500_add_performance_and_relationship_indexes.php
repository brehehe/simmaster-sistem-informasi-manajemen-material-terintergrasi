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
        if (Schema::hasTable('last_stocks')) {
            Schema::table('last_stocks', function (Blueprint $table) {
                $table->index(['police_station_id', 'type_id', 'date'], 'idx_last_stocks_ps_type_date');
            });
        }

        if (Schema::hasTable('last_stock_details')) {
            Schema::table('last_stock_details', function (Blueprint $table) {
                $table->index(['last_stock_id', 'type_id'], 'idx_lsd_ls_type');
            });
        }

        if (Schema::hasTable('receptions')) {
            Schema::table('receptions', function (Blueprint $table) {
                $table->index(['police_station_id', 'date'], 'idx_receptions_ps_date');
                $table->index(['regional_police_id', 'date'], 'idx_receptions_reg_date');
            });
        }

        if (Schema::hasTable('reception_details')) {
            Schema::table('reception_details', function (Blueprint $table) {
                $table->index(['reception_id', 'type_id'], 'idx_reception_details_rec_type');
            });
        }

        if (Schema::hasTable('material_damages')) {
            Schema::table('material_damages', function (Blueprint $table) {
                $table->index(['police_station_id', 'date'], 'idx_mat_damages_ps_date');
                $table->index(['regional_police_id', 'date'], 'idx_mat_damages_reg_date');
            });
        }

        if (Schema::hasTable('material_damage_details')) {
            Schema::table('material_damage_details', function (Blueprint $table) {
                $table->index(['material_damage_id', 'type_id'], 'idx_mdd_damage_type');
            });
        }

        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->index(['receiver_police_station_id', 'is_read', 'created_at'], 'idx_messages_recv_ps_read');
                $table->index(['receiver_regional_police_id', 'is_read', 'created_at'], 'idx_messages_recv_reg_read');
                $table->index(['sender_id', 'created_at'], 'idx_messages_sender_created');
            });
        }

        if (Schema::hasTable('material_subsidies')) {
            Schema::table('material_subsidies', function (Blueprint $table) {
                $table->index(['police_station_id', 'subsidy_date'], 'idx_mat_subsidies_ps_date');
            });
        }

        if (Schema::hasTable('material_usage_detail_items')) {
            Schema::table('material_usage_detail_items', function (Blueprint $table) {
                $table->index(['type_id', 'service_id'], 'idx_mudi_type_service');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('material_usage_detail_items')) {
            Schema::table('material_usage_detail_items', function (Blueprint $table) {
                $table->dropIndex('idx_mudi_type_service');
            });
        }

        if (Schema::hasTable('material_subsidies')) {
            Schema::table('material_subsidies', function (Blueprint $table) {
                $table->dropIndex('idx_mat_subsidies_ps_date');
            });
        }

        if (Schema::hasTable('messages')) {
            Schema::table('messages', function (Blueprint $table) {
                $table->dropIndex('idx_messages_recv_ps_read');
                $table->dropIndex('idx_messages_recv_reg_read');
                $table->dropIndex('idx_messages_sender_created');
            });
        }

        if (Schema::hasTable('material_damage_details')) {
            Schema::table('material_damage_details', function (Blueprint $table) {
                $table->dropIndex('idx_mdd_damage_type');
            });
        }

        if (Schema::hasTable('material_damages')) {
            Schema::table('material_damages', function (Blueprint $table) {
                $table->dropIndex('idx_mat_damages_ps_date');
                $table->dropIndex('idx_mat_damages_reg_date');
            });
        }

        if (Schema::hasTable('reception_details')) {
            Schema::table('reception_details', function (Blueprint $table) {
                $table->dropIndex('idx_reception_details_rec_type');
            });
        }

        if (Schema::hasTable('receptions')) {
            Schema::table('receptions', function (Blueprint $table) {
                $table->dropIndex('idx_receptions_ps_date');
                $table->dropIndex('idx_receptions_reg_date');
            });
        }

        if (Schema::hasTable('last_stock_details')) {
            Schema::table('last_stock_details', function (Blueprint $table) {
                $table->dropIndex('idx_lsd_ls_type');
            });
        }

        if (Schema::hasTable('last_stocks')) {
            Schema::table('last_stocks', function (Blueprint $table) {
                $table->dropIndex('idx_last_stocks_ps_type_date');
            });
        }
    }
};
