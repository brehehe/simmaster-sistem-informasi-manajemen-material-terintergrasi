<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_usages', function (Blueprint $table) {
            $table->boolean('reporting_complete')->default(false);
            $table->json('reporting_keys')->nullable();
        });
        Schema::table('receptions', function (Blueprint $table) {
            $table->string('ordonatur_nrp')->nullable();
            $table->text('kasi_signature')->nullable();
            $table->text('director_signature')->nullable();
        });
        Schema::table('types', fn (Blueprint $table) => $table->string('unit', 30)->default('Unit'));
        Schema::table('material_shipments', fn (Blueprint $table) => $table->timestamp('stock_deducted_at')->nullable());
    }

    public function down(): void
    {
        Schema::table('material_usages', fn (Blueprint $table) => $table->dropColumn(['reporting_complete', 'reporting_keys']));
        Schema::table('receptions', fn (Blueprint $table) => $table->dropColumn(['ordonatur_nrp', 'kasi_signature', 'director_signature']));
        Schema::table('types', fn (Blueprint $table) => $table->dropColumn('unit'));
        Schema::table('material_shipments', fn (Blueprint $table) => $table->dropColumn('stock_deducted_at'));
    }
};
