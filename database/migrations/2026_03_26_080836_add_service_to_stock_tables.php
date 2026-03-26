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
        Schema::table('stocks', function (Blueprint $table) {
            $table->foreignUuid('service_id')->nullable()->after('type_detail_id')->constrained('services')->nullOnDelete();
            $table->foreignUuid('service_detail_id')->nullable()->after('service_id')->constrained('service_details')->nullOnDelete();
        });

        Schema::table('stock_details', function (Blueprint $table) {
            $table->foreignUuid('service_id')->nullable()->after('type_detail_id')->constrained('services')->nullOnDelete();
            $table->foreignUuid('service_detail_id')->nullable()->after('service_id')->constrained('service_details')->nullOnDelete();
        });

        Schema::table('history_stocks', function (Blueprint $table) {
            $table->foreignUuid('service_id')->nullable()->after('type_detail_id')->constrained('services')->nullOnDelete();
            $table->foreignUuid('service_detail_id')->nullable()->after('service_id')->constrained('service_details')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_stocks', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['service_detail_id']);
            $table->dropColumn(['service_id', 'service_detail_id']);
        });

        Schema::table('stock_details', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['service_detail_id']);
            $table->dropColumn(['service_id', 'service_detail_id']);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropForeign(['service_detail_id']);
            $table->dropColumn(['service_id', 'service_detail_id']);
        });
    }
};
