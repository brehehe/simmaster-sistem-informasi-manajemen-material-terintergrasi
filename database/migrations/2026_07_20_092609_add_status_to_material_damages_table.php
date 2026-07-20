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
        Schema::table('material_damages', function (Blueprint $table) {
            if (!Schema::hasColumn('material_damages', 'status')) {
                $table->enum('status', ['reported', 'under_review', 'approved', 'disposed'])->default('approved')->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('material_damages', function (Blueprint $table) {
            if (Schema::hasColumn('material_damages', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
