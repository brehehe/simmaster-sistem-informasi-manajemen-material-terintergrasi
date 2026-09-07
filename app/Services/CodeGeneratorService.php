<?php

namespace App\Services;

use App\Models\MenuPolda\RackAssignment\RackAssignment;
use App\Models\MenuPolda\MaterialDamage\MaterialDamage;
use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\MenuPolda\MutationStock\MutationStock;
use App\Models\MenuPolda\MaterialSubsidy\MaterialSubsidy;
use App\Models\StockOpname\StockOpname;
use App\Models\Reception\Reception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CodeGeneratorService
{
    /**
     * Acquire a transactional advisory lock for concurrency safety.
     * Automatically released when the surrounding DB::transaction commits or rolls back.
     */
    public static function acquireLock(string $lockKey): void
    {
        try {
            $driver = DB::connection()->getDriverName();
            if ($driver === 'pgsql') {
                DB::statement('SELECT pg_advisory_xact_lock(hashtext(?))', [$lockKey]);
            } elseif ($driver === 'mysql') {
                DB::statement('SELECT GET_LOCK(?, 5)', [$lockKey]);
            }
        } catch (\Throwable $e) {
            // Non-blocking fallback if locking is not supported
        }
    }

    /**
     * Execute a database transaction with automatic retry on unique constraint collision.
     *
     * @template T
     * @param callable(): T $callback
     * @param int $maxAttempts
     * @return T
     */
    public static function runTransaction(callable $callback, int $maxAttempts = 3)
    {
        $attempts = 0;
        while ($attempts < $maxAttempts) {
            $attempts++;
            try {
                return DB::transaction($callback);
            } catch (\Throwable $e) {
                $isUniqueViolation = $e instanceof \Illuminate\Database\UniqueConstraintViolationException
                    || str_contains($e->getMessage(), '23505')
                    || str_contains($e->getMessage(), 'unique constraint')
                    || str_contains($e->getMessage(), 'duplicate key')
                    || str_contains($e->getMessage(), 'Duplicate entry');

                if ($isUniqueViolation && $attempts < $maxAttempts) {
                    usleep(rand(20000, 80000)); // Sleep 20ms - 80ms before retrying
                    continue;
                }
                throw $e;
            }
        }
    }

    /**
     * Generate unique prefix-date-number sequence for any model.
     *
     * @param class-string<Model> $modelClass
     * @param string $prefix
     * @param int $padding
     * @return string
     */
    public static function generate(string $modelClass, string $prefix, int $padding = 4): string
    {
        self::acquireLock("codegen_{$prefix}");

        $date = now()->format('Ymd');
        $fullPrefix = "{$prefix}-{$date}-";

        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = method_exists($modelClass, 'withTrashed') ? $modelClass::withTrashed() : $modelClass::query();

        $lastRecord = $query
            ->where('code', 'like', $fullPrefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastRecord && preg_match('/-(\d+)$/', (string)$lastRecord->code, $matches)) {
            $nextNumber = (int)$matches[1] + 1;
        }

        $code = $fullPrefix . str_pad($nextNumber, $padding, '0', STR_PAD_LEFT);

        // Safeguard collision loop to guarantee absolute uniqueness with fresh query check
        $existsQuery = fn($c) => (method_exists($modelClass, 'withTrashed') ? $modelClass::withTrashed() : $modelClass::query())->where('code', $c)->exists();
        while ($existsQuery($code)) {
            $nextNumber++;
            $code = $fullPrefix . str_pad($nextNumber, $padding, '0', STR_PAD_LEFT);
        }

        return $code;
    }
}
