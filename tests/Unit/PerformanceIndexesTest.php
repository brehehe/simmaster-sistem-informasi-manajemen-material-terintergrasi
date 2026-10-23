<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceIndexesTest extends TestCase
{
    /**
     * Test that performance and relationship indexes exist in PostgreSQL database.
     */
    public function test_required_performance_indexes_exist(): void
    {
        $expectedIndexes = [
            'idx_last_stocks_ps_type_date',
            'idx_lsd_ls_type',
            'idx_receptions_ps_date',
            'idx_receptions_reg_date',
            'idx_reception_details_rec_type',
            'idx_mat_damages_ps_date',
            'idx_mat_damages_reg_date',
            'idx_mdd_damage_type',
            'idx_messages_recv_ps_read',
            'idx_messages_recv_reg_read',
            'idx_messages_sender_created',
            'idx_mat_subsidies_ps_date',
            'idx_mudi_type_service',
        ];

        try {
            $dbName = config('database.connections.pgsql.database');
            if ($dbName === ':memory:') {
                config(['database.connections.pgsql.database' => 'fazmat']);
                DB::purge('pgsql');
            }

            $existingIndexes = collect(DB::connection('pgsql')->select("SELECT indexname FROM pg_indexes WHERE schemaname = 'public'"))
                ->pluck('indexname')
                ->toArray();
        } catch (\Throwable $e) {
            $this->markTestSkipped('PostgreSQL connection not reachable in this test runner: ' . $e->getMessage());
        }

        foreach ($expectedIndexes as $indexName) {
            $this->assertContains(
                $indexName,
                $existingIndexes,
                "Expected database index '{$indexName}' was not found in pg_indexes."
            );
        }
    }
}
