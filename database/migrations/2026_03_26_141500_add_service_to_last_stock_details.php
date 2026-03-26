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
        Schema::table('last_stocks', function (Blueprint $table) {
            $table->foreignUuid('type_id')->nullable()->after('date');
        });

        Schema::table('last_stock_details', function (Blueprint $table) {
            $table->foreignUuid('service_id')->nullable()->after('type_detail_id');
            $table->foreignUuid('service_detail_id')->nullable()->after('service_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('last_stocks', function (Blueprint $table) {
            $table->dropColumn('type_id');
        });

        Schema::table('last_stock_details', function (Blueprint $table) {
            $table->dropColumn(['service_id', 'service_detail_id']);
        });
    }
};
