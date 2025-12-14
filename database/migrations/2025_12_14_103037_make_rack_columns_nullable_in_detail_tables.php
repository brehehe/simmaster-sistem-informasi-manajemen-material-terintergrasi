<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Make to_rack_id nullable in rack_assignment_details for "Tanpa Rak" support
        Schema::table('rack_assignment_details', function (Blueprint $table) {
            $table->uuid('to_rack_id')->nullable()->change();
            $table->uuid('from_rack_id')->nullable()->change();
        });

        // Make type_detail_id nullable in all detail tables
        Schema::table('rack_assignment_details', function (Blueprint $table) {
            $table->uuid('type_detail_id')->nullable()->change();
        });

        Schema::table('material_usage_details', function (Blueprint $table) {
            $table->uuid('type_detail_id')->nullable()->change();
        });

        Schema::table('material_damage_details', function (Blueprint $table) {
            $table->uuid('type_detail_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Rollback not needed
    }
};
