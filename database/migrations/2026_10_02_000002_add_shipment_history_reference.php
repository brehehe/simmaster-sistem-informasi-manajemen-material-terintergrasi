<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('history_stocks', 'material_shipment_id')) {
            Schema::table('history_stocks', fn (Blueprint $table) => $table->uuid('material_shipment_id')->nullable()->index());
        }
        foreach (['BPKB' => 'Buku', 'E-BPKB' => 'Buku', 'STNK' => 'Lembar', 'STCK' => 'Lembar', 'SIM CARD' => 'Kartu'] as $name => $unit) {
            DB::table('types')->where('name', $name)->where('unit', 'Unit')->update(['unit' => $unit]);
        }
        DB::table('types')->where('unit', 'Unit')->where(function ($q) {
            $q->where('name', 'like', 'TNKB%')->orWhere('name', 'like', 'TCKB%');
        })->update(['unit' => 'Set']);
    }

    public function down(): void
    {
        Schema::table('history_stocks', fn (Blueprint $table) => $table->dropColumn('material_shipment_id'));
    }
};
