<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Note: PostgreSQL doesn't support CHECK constraints in Laravel the same way
        // We'll add them using raw SQL

        // Add indexes for performance
        Schema::table('stocks', function (Blueprint $table) {
            $table->index(['regional_police_id', 'police_station_id', 'type_id', 'type_detail_id'], 'idx_stocks_owner');
            $table->index(['type_id', 'type_detail_id'], 'idx_stocks_type');
        });

        Schema::table('stock_details', function (Blueprint $table) {
            $table->index(['regional_police_id', 'police_station_id', 'stock_id'], 'idx_stock_details_owner');
            $table->index(['rack_id', 'police_station_id'], 'idx_stock_details_rack');
            $table->index(['type_id', 'type_detail_id'], 'idx_stock_details_type');
        });

        Schema::table('history_stocks', function (Blueprint $table) {
            $table->index(['regional_police_id', 'police_station_id', 'date', 'status_type'], 'idx_history_stocks_owner');
            $table->index(['status_type', 'date'], 'idx_history_stocks_ref');
            $table->index(['type_id', 'type_detail_id', 'date'], 'idx_history_stocks_type_date');
        });

        // Add CHECK constraints using raw SQL for PostgreSQL
        DB::statement('
            ALTER TABLE stocks
            ADD CONSTRAINT chk_stocks_owner
            CHECK (
                (regional_police_id IS NOT NULL AND police_station_id IS NULL) OR
                (regional_police_id IS NULL AND police_station_id IS NOT NULL)
            )
        ');

        DB::statement('
            ALTER TABLE stock_details
            ADD CONSTRAINT chk_stock_details_owner
            CHECK (
                (regional_police_id IS NOT NULL AND police_station_id IS NULL) OR
                (regional_police_id IS NULL AND police_station_id IS NOT NULL)
            )
        ');

        DB::statement('
            ALTER TABLE history_stocks
            ADD CONSTRAINT chk_history_stocks_owner
            CHECK (
                (regional_police_id IS NOT NULL AND police_station_id IS NULL) OR
                (regional_police_id IS NULL AND police_station_id IS NOT NULL) OR
                (regional_police_id IS NULL AND police_station_id IS NULL)
            )
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop CHECK constraints
        DB::statement('ALTER TABLE stocks DROP CONSTRAINT IF EXISTS chk_stocks_owner');
        DB::statement('ALTER TABLE stock_details DROP CONSTRAINT IF EXISTS chk_stock_details_owner');
        DB::statement('ALTER TABLE history_stocks DROP CONSTRAINT IF EXISTS chk_history_stocks_owner');

        // Drop indexes
        Schema::table('stocks', function (Blueprint $table) {
            $table->dropIndex('idx_stocks_owner');
            $table->dropIndex('idx_stocks_type');
        });

        Schema::table('stock_details', function (Blueprint $table) {
            $table->dropIndex('idx_stock_details_owner');
            $table->dropIndex('idx_stock_details_rack');
            $table->dropIndex('idx_stock_details_type');
        });

        Schema::table('history_stocks', function (Blueprint $table) {
            $table->dropIndex('idx_history_stocks_owner');
            $table->dropIndex('idx_history_stocks_ref');
            $table->dropIndex('idx_history_stocks_type_date');
        });
    }
};
