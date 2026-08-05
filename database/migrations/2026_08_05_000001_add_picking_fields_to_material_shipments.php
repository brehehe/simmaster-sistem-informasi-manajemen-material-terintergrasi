<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom serah terima warehouse (picking) ke material_shipments:
     * - Identitas pengambil (nama, pangkat, jabatan)
     * - TTD Digital (base64)
     * - Foto Dokumentasi (path)
     * - Waktu serah terima
     */
    public function up(): void
    {
        Schema::table('material_shipments', function (Blueprint $table) {
            $table->string('picker_name')->nullable()->after('received_by')
                ->comment('Nama pengambil material di warehouse');
            $table->string('picker_rank')->nullable()->after('picker_name')
                ->comment('Pangkat pengambil');
            $table->string('picker_position')->nullable()->after('picker_rank')
                ->comment('Jabatan pengambil');
            $table->longText('picker_signature')->nullable()->after('picker_position')
                ->comment('TTD digital (base64 PNG)');
            $table->string('picker_photo', 500)->nullable()->after('picker_signature')
                ->comment('Path foto dokumentasi serah terima');
            $table->timestamp('picked_at')->nullable()->after('picker_photo')
                ->comment('Waktu serah terima di warehouse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_shipments', function (Blueprint $table) {
            $table->dropColumn([
                'picker_name',
                'picker_rank',
                'picker_position',
                'picker_signature',
                'picker_photo',
                'picked_at',
            ]);
        });
    }
};
