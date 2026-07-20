<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_subsidies', function (Blueprint $table) {
            $table->foreignUuid('police_station_id')->nullable()->after('regional_police_id')->constrained('police_stations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('material_subsidies', function (Blueprint $table) {
            $table->dropForeign(['police_station_id']);
            $table->dropColumn('police_station_id');
        });
    }
};
