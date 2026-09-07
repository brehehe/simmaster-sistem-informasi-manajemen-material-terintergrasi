<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use App\Models\Target\TargetDetail;
use App\Models\User\UserType;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Mapping spesifik untuk menyatukan tipe-tipe duplikat ke ID kanonikal (resmi):
        $canonicalMap = [
            '01a0150c-64cf-72eb-8bbe-abd14878dba6' => '019d2885-c382-737c-a493-f0472c77a71f', // SIM CARD
            '01a01501-38ac-7035-8310-524e73bb3742' => '019d2885-c382-737c-a493-f0472c77a71f', // SIM CARD (trashed)
            '01a01565-d23e-734f-abc1-30d63b0aa792' => '019d2885-c384-7261-92dc-c931fc3a2d1b', // STNK
            '01a01604-a8da-7256-9df7-15f09cfdbd8a' => '019d2885-c385-7135-8def-d1ed200dfb56', // STCK
            '01a01537-102c-7251-98a4-6bbcf27ac68d' => '019d2885-c386-7307-8f75-4c2820133e2b', // E-BPKB (trashed)
            '01a01527-93e0-7174-84ee-cd3fa08711ed' => '019d2885-c387-71e9-8371-e98987221fa1', // BPKB
            '01a01608-88e4-72d3-8ccb-553599ddd76e' => '019d2885-c388-7278-85d9-8462caa126d0', // MUTASI
        ];

        // Temukan juga jika ada duplikat dinamis lainnya berdasarkan nama
        $duplicateGroups = DB::table('types')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('count(*) > 1')
            ->pluck('name');

        foreach ($duplicateGroups as $name) {
            $types = Type::withTrashed()->where('name', $name)->orderBy('created_at')->get();
            if ($types->count() < 2) {
                continue;
            }

            // Pilih primary: yang ada di canonical map atau yang tertua
            $primary = $types->first(fn($t) => in_array($t->id, array_values($canonicalMap))) ?: $types->first();
            $secondaries = $types->reject(fn($t) => $t->id === $primary->id);

            foreach ($secondaries as $sec) {
                if (!isset($canonicalMap[$sec->id])) {
                    $canonicalMap[$sec->id] = $primary->id;
                }
            }
        }

        // 2. Proses migrasi relasi data untuk setiap tipe sekunder ke tipe kanonikal
        $relatedTables = [
            'type_details',
            'services',
            'stock_details',
            'history_stocks',
            'history_stock_details',
            'last_stocks',
            'last_stock_details',
            'material_damages',
            'material_damage_details',
            'material_shipments',
            'material_shipment_details',
            'material_subsidies',
            'material_subsidy_details',
            'material_usages',
            'material_usage_details',
            'material_usage_detail_items',
            'mutation_stocks',
            'mutation_stock_details',
            'rack_assignments',
            'rack_assignment_details',
            'receptions',
            'reception_details',
            'reception_detail_items',
            'stock_opnames',
            'stock_opname_details',
        ];

        foreach ($canonicalMap as $oldId => $newId) {
            // Pindahkan relasi pada tabel-tabel terkait
            foreach ($relatedTables as $table) {
                if (Schema::hasTable($table) && Schema::hasColumn($table, 'type_id')) {
                    try {
                        DB::table($table)->where('type_id', $oldId)->update(['type_id' => $newId]);
                    } catch (\Exception $e) {
                        // Abaikan jika tabel tidak ada
                    }
                }
            }

            // Tangani target_details dengan deduplikasi
            if (Schema::hasTable('target_details')) {
                $targetDetails = DB::table('target_details')->where('type_id', $oldId)->get();
                foreach ($targetDetails as $td) {
                    $existing = DB::table('target_details')
                        ->where('target_id', $td->target_id)
                        ->where('type_id', $newId)
                        ->where('type_detail_id', $td->type_detail_id)
                        ->first();

                    if ($existing) {
                        DB::table('target_details')->where('id', $td->id)->delete();
                    } else {
                        DB::table('target_details')->where('id', $td->id)->update(['type_id' => $newId]);
                    }
                }
            }

            // Update JSON types pada user_types
            if (Schema::hasTable('user_types') && Schema::hasColumn('user_types', 'types')) {
                $userTypes = DB::table('user_types')->where('types', 'like', '%' . $oldId . '%')->get();
                foreach ($userTypes as $ut) {
                    $typesArr = json_decode($ut->types, true) ?: [];
                    $updatedTypes = array_map(fn($tid) => $tid === $oldId ? $newId : $tid, $typesArr);
                    $updatedTypes = array_values(array_unique($updatedTypes));
                    DB::table('user_types')->where('id', $ut->id)->update(['types' => json_encode($updatedTypes)]);
                }
            }

            // Hapus record tipe sekunder
            DB::table('types')->where('id', $oldId)->delete();
        }

        // 3. Konsolidasi tabel stocks: gabungkan header stock dengan kombinasi lokasi & tipe yang identik
        if (Schema::hasTable('stocks') && Schema::hasTable('stock_details')) {
            $allStocks = DB::table('stocks')->get();
            $groupedStocks = $allStocks->groupBy(function($s) {
                return sprintf(
                    '%s|%s|%s|%s',
                    $s->type_id,
                    $s->type_detail_id ?? 'NULL',
                    $s->regional_police_id ?? 'NULL',
                    $s->police_station_id ?? 'NULL'
                );
            });

            foreach ($groupedStocks as $group) {
                if ($group->count() > 1) {
                    $primaryStock = $group->first();
                    $duplicateStockIds = $group->slice(1)->pluck('id')->toArray();

                    // Alihkan relasi stock_details ke primary stock
                    DB::table('stock_details')->whereIn('stock_id', $duplicateStockIds)->update(['stock_id' => $primaryStock->id]);

                    // Hapus header stock yang duplikat
                    DB::table('stocks')->whereIn('id', $duplicateStockIds)->delete();
                }
            }

            // Hitung ulang seluruh quantity pada header stocks agar sinkron dengan stock_details
            $remainingStocks = DB::table('stocks')->get();
            foreach ($remainingStocks as $stk) {
                $actualQty = DB::table('stock_details')
                    ->where('stock_id', $stk->id)
                    ->where('is_active', true)
                    ->sum('quantity');
                DB::table('stocks')->where('id', $stk->id)->update(['quantity' => $actualQty]);
            }
        }

        // 4. Pasang Unique Index pada tabel types agar material kembar tidak dapat terbuat lagi
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS types_name_unique_idx ON types (name) WHERE deleted_at IS NULL;');
            }
        } catch (\Exception $e) {
            // Abaikan jika sudah ada
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('DROP INDEX IF EXISTS types_name_unique_idx;');
            }
        } catch (\Exception $e) {
            // Abaikan
        }
    }
};
