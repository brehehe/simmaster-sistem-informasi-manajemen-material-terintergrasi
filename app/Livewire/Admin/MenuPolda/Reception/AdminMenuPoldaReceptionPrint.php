<?php

namespace App\Livewire\Admin\MenuPolda\Reception;

use App\Models\Reception\Reception;
use Livewire\Component;

class AdminMenuPoldaReceptionPrint extends Component
{
    public $receptionId;
    public $reception;

    public function mount($id)
    {
        $this->receptionId = $id;
        $this->reception = Reception::with([
            'regionalPolice', 
            'policeStation', 
            'typeMaterial',
            'receptionDetails.receptionDetailItems.typeDetail',
            'receptionDetails.receptionDetailItems.service',
            'receptionDetails.receptionDetailItems.serviceDetail'
        ])->findOrFail($id);
    }

    public function render()
    {
        $rawItems = $this->reception->receptionDetails->flatMap(function($detail) {
            return $detail->receptionDetailItems;
        });

        $groupedItems = [];
        foreach($rawItems as $item) {
            $typeId = $item->type_detail_id ?? 'GLOBAL';
            $typeName = $item->typeDetail->name ?? $this->reception->typeMaterial?->name ?? '-';
            $basePrice = $item->typeDetail->price ?? 0;

            if(!isset($groupedItems[$typeId])) {
                $groupedItems[$typeId] = [
                    'name' => $typeName,
                    'base_price' => $basePrice,
                    'total_quantity' => 0,
                    'sub_items' => []
                ];
            }

            if (!$item->service_id) {
                // Main item without service breakdown
                $groupedItems[$typeId]['total_quantity'] += $item->quantity;
            } else {
                // Item with service/details sub-breakdown
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

        // Sort alphabetically by Material Detail Name
        usort($groupedItems, function($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        // Use a clean slate for the layout without navigation bars
        return view('livewire.admin.menu-polda.reception.admin-menu-polda-reception-print', [
            'receptionDetails' => $groupedItems
        ])->layout('components.layouts.print', ['title' => 'Surat Perintah Pengeluaran Materiel - ' . $this->reception->code]);
    }

    public function terbilang($angka) {
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
}
