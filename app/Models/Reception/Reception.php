<?php

namespace App\Models\Reception;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reception extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'sppm_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function regionalPolice()
    {
        return $this->belongsTo(RegionalPolice::class);
    }

    public function policeStation()
    {
        return $this->belongsTo(PoliceStation::class);
    }

    public function receptionDetails()
    {
        return $this->hasMany(ReceptionDetail::class);
    }

    public function typeMaterial()
    {
        return $this->belongsTo(\App\Models\Type\Type::class,'type_id','id');
    }

    /**
     * Get grouped items with main materials and sub-breakdowns (supporting/service details)
     */
    public function getGroupedItems()
    {
        $rawItems = $this->receptionDetails->flatMap(function($detail) {
            return $detail->receptionDetailItems;
        });

        $groupedItems = [];
        foreach($rawItems as $item) {
            $typeId = $item->type_detail_id ?? $item->type_id ?? 'GLOBAL';
            $typeName = $item->typeDetail->name ?? $item->type->name ?? '-';
            $basePrice = $item->typeDetail->price ?? $item->type->price ?? 0;

            if(!isset($groupedItems[$typeId])) {
                $groupedItems[$typeId] = [
                    'name' => $typeName,
                    'base_price' => $basePrice,
                    'total_quantity' => 0,
                    'sub_items' => []
                ];
            }

            if (!$item->service_id) {
                $groupedItems[$typeId]['total_quantity'] += $item->quantity;
            } else {
                $subKey = $item->service_id . '_' . ($item->service_detail_id ?? '0');
                if (!isset($groupedItems[$typeId]['sub_items'][$subKey])) {
                    $subName = $item->service->name;
                    if ($item->serviceDetail) {
                        $subName .= ' (' . $item->serviceDetail->name . ')';
                    }
                    $groupedItems[$typeId]['sub_items'][$subKey] = [
                        'name' => $subName,
                        'quantity' => 0,
                        'price' => $item->serviceDetail?->price ?? $item->service?->price ?? $basePrice,
                    ];
                }
                $groupedItems[$typeId]['sub_items'][$subKey]['quantity'] += $item->quantity;
            }
        }

        usort($groupedItems, function($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return $groupedItems;
    }

    /**
     * Convert number to Indonesian word representation
     */
    public function terbilang($angka)
    {
        $angka = abs(floor($angka));
        $baca = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
        $terbilang = "";
        if ($angka < 12) {
            $terbilang = " " . $baca[$angka];
        } else if ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10) . " Belas";
        } else if ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10) . " Puluh" . $this->terbilang($angka % 10);
        } else if ($angka < 200) {
            $terbilang = " Seratus" . $this->terbilang($angka - 100);
        } else if ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100) . " Ratus" . $this->terbilang($angka % 100);
        } else if ($angka < 2000) {
            $terbilang = " Seribu" . $this->terbilang($angka - 1000);
        } else if ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000) . " Ribu" . $this->terbilang($angka % 1000);
        } else if ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000) . " Juta" . $this->terbilang($angka % 1000000);
        }
        return trim($terbilang);
    }

    /**
     * Generate unique code for Reception
     */
    public static function generateCode()
    {
        $date = now()->format('Ymd');
        $lastRecord = self::withTrashed()
            ->whereDate('created_at', now()->toDateString())
            ->latest('created_at')
            ->first();

        if ($lastRecord) {
            $lastNumber = (int) substr($lastRecord->code, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return 'RC-' . $date . '-' . $newNumber;
    }
}
