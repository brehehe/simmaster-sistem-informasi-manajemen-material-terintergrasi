<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add parent_id to types table
        Schema::table('types', function (Blueprint $table) {
            $table->foreignUuid('parent_id')->nullable()->after('id')->constrained('types')->onDelete('cascade');
        });

        // 2. Add BAPPM and SPPM fields to receptions table
        Schema::table('receptions', function (Blueprint $table) {
            $table->date('sppm_date')->nullable()->after('date');
            $table->string('bappm_number')->nullable()->after('sppm_date');
            
            // Commission member 1
            $table->string('commission_member_1_name')->nullable()->after('bappm_number');
            $table->string('commission_member_1_rank')->nullable()->after('commission_member_1_name');
            $table->string('commission_member_1_nip')->nullable()->after('commission_member_1_rank');
            $table->string('commission_member_1_position')->nullable()->after('commission_member_1_nip');

            // Commission member 2
            $table->string('commission_member_2_name')->nullable()->after('commission_member_1_position');
            $table->string('commission_member_2_rank')->nullable()->after('commission_member_2_name');
            $table->string('commission_member_2_nip')->nullable()->after('commission_member_2_rank');
            $table->string('commission_member_2_position')->nullable()->after('commission_member_2_nip');

            // Commission member 3
            $table->string('commission_member_3_name')->nullable()->after('commission_member_2_position');
            $table->string('commission_member_3_rank')->nullable()->after('commission_member_3_name');
            $table->string('commission_member_3_nip')->nullable()->after('commission_member_3_rank');
            $table->string('commission_member_3_position')->nullable()->after('commission_member_3_nip');

            // Kasi Fasmat
            $table->string('kasi_fasmat_name')->nullable()->after('commission_member_3_position');
            $table->string('kasi_fasmat_rank')->nullable()->after('kasi_fasmat_name');
            $table->string('kasi_fasmat_nip')->nullable()->after('kasi_fasmat_rank');

            // Ordonatur
            $table->string('ordonatur_name')->nullable()->after('kasi_fasmat_nip');
            $table->string('ordonatur_rank')->nullable()->after('ordonatur_name');
        });

        // 3. Seed supporting materials: YMCKT and LAMINASI under SIM CARD
        $simCard = DB::table('types')->where('name', 'SIM CARD')->first();
        if ($simCard) {
            DB::table('types')->insert([
                [
                    'id' => Str::uuid()->toString(),
                    'name' => 'YMCKT',
                    'is_with_serial_number' => false,
                    'price' => 0.00,
                    'description' => 'Materiil Pendukung SIM Card (YMCKT)',
                    'is_active' => true,
                    'parent_id' => $simCard->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'id' => Str::uuid()->toString(),
                    'name' => 'LAMINASI',
                    'is_with_serial_number' => false,
                    'price' => 0.00,
                    'description' => 'Materiil Pendukung SIM Card (Laminating)',
                    'is_active' => true,
                    'parent_id' => $simCard->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Remove seeded types
        $simCard = DB::table('types')->where('name', 'SIM CARD')->first();
        if ($simCard) {
            DB::table('types')->where('parent_id', $simCard->id)->delete();
        }

        // 2. Remove columns from receptions table
        Schema::table('receptions', function (Blueprint $table) {
            $table->dropColumn([
                'sppm_date',
                'bappm_number',
                'commission_member_1_name',
                'commission_member_1_rank',
                'commission_member_1_nip',
                'commission_member_1_position',
                'commission_member_2_name',
                'commission_member_2_rank',
                'commission_member_2_nip',
                'commission_member_2_position',
                'commission_member_3_name',
                'commission_member_3_rank',
                'commission_member_3_nip',
                'commission_member_3_position',
                'kasi_fasmat_name',
                'kasi_fasmat_rank',
                'kasi_fasmat_nip',
                'ordonatur_name',
                'ordonatur_rank',
            ]);
        });

        // 3. Remove parent_id column from types table
        Schema::table('types', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
