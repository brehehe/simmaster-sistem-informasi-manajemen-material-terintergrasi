<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryStockDetail extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    public static function generateCode(string $prefix = 'HSD'): string
    {
        do {
            $date = now()->format('Ymd');
            $code = $prefix . '-' . $date . '-' . bin2hex(random_bytes(4));
        } while (self::withTrashed()->where('code', $code)->exists());

        return $code;
    }
}
