<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
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
        // 1. Temukan nama-nama material yang duplikat di tabel types
        $duplicateNames = DB::table('types')
            ->whereNull('deleted_at')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('count(*) > 1')
            ->pluck('name');

        foreach ($duplicateNames as $name) {
            $types = Type::where('name', $name)->orderBy('created_at')->get();
            if ($types->count() < 2) {
                continue;
            }

            // Pilih primary: yang memiliki stok, atau yang pertama dibuat
            $primary = $types->first(fn($t) => DB::table('stocks')->where('type_id', $t->id)->exists()) ?: $types->first();
            $secondaries = $types->reject(fn($t) => $t->id === $primary->id);

            foreach ($secondaries as $sec) {
                // Relasikan type_details ke primary
                TypeDetail::where('type_id', $sec->id)->update(['type_id' => $primary->id]);

                // Relasikan target_details ke primary
                TargetDetail::where('type_id', $sec->id)->update(['type_id' => $primary->id]);

                // Update relasi di tabel-tabel transaksi lain jika ada
                $relatedTables = [
                    'stocks',
                    'stock_details',
                    'history_stocks',
                    'receptions',
                    'reception_details',
                    'reception_detail_items',
                    'material_shipment_details',
                    'material_usage_details',
                    'material_usage_detail_items',
                    'material_damage_details',
                    'material_subsidy_details',
                    'last_stock_details',
                    'rack_assignment_details',
                    'stock_opname_details',
                ];

                foreach ($relatedTables as $table) {
                    try {
                        DB::table($table)->where('type_id', $sec->id)->update(['type_id' => $primary->id]);
                    } catch (\Exception $e) {
                        // Lanjutkan jika tabel atau kolom tidak ada
                    }
                }

                // Update JSON types pada user_types
                foreach (UserType::all() as $ut) {
                    if ($ut->types && is_array($ut->types) && in_array($sec->id, $ut->types)) {
                        $newTypes = array_values(array_unique(array_map(
                            fn($tid) => $tid === $sec->id ? $primary->id : $tid,
                            $ut->types
                        )));
                        $ut->update(['types' => $newTypes]);
                    }
                }

                // Hapus baris duplikat sekunder
                $sec->forceDelete();
            }
        }

        // 2. Pasang Unique Index pada name untuk mencegah duplikasi di kemudian hari (PostgreSQL / SQLite compatible)
        try {
            if (DB::getDriverName() === 'pgsql') {
                DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS types_name_unique_idx ON types (name) WHERE deleted_at IS NULL;');
            }
        } catch (\Exception $e) {
            // Abaikan jika index sudah ada
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
