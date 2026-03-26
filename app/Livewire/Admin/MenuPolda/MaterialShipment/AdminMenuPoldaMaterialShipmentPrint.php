<?php

namespace App\Livewire\Admin\MenuPolda\MaterialShipment;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use Livewire\Component;

class AdminMenuPoldaMaterialShipmentPrint extends Component
{
    public $shipmentId;
    public $shipment;

    public function mount($id)
    {
        $this->shipmentId = $id;
        $this->shipment = MaterialShipment::with([
            'senderRegionalPolice',
            'receiverPoliceStation',
            'materialShipmentDetails.type',
            'materialShipmentDetails.typeDetail',
            'materialShipmentDetails.stockDetail.service',
            'materialShipmentDetails.stockDetail.serviceDetail',
        ])->findOrFail($id);
    }

    public function render()
    {
        $groupedItems = [];
        
        foreach ($this->shipment->materialShipmentDetails as $item) {
            $typeDetailId = $item->type_detail_id ?? 'GLOBAL';
            $typeDetailName = $item->typeDetail->name ?? $item->type->name ?? '-';
            $basePrice = $item->typeDetail->price ?? $item->type->price ?? 0;

            if (!isset($groupedItems[$typeDetailId])) {
                $groupedItems[$typeDetailId] = [
                    'name' => $typeDetailName,
                    'base_price' => $basePrice,
                    'total_quantity' => 0,
                    'sub_items' => []
                ];
            }

            $service = $item->stockDetail->service ?? null;
            $serviceDetail = $item->stockDetail->serviceDetail ?? null;

            if (!$service) {
                $groupedItems[$typeDetailId]['total_quantity'] += (float)$item->quantity;
            } else {
                $subKey = $service->id . '_' . ($serviceDetail->id ?? '0');
                if (!isset($groupedItems[$typeDetailId]['sub_items'][$subKey])) {
                    $subName = $service->name;
                    if ($serviceDetail) {
                        $subName .= ' (' . $serviceDetail->name . ')';
                    }
                    $groupedItems[$typeDetailId]['sub_items'][$subKey] = [
                        'name' => $subName,
                        'quantity' => 0,
                        'price' => (float)($serviceDetail->price ?? $service->price ?? $basePrice),
                    ];
                }
                $groupedItems[$typeDetailId]['sub_items'][$subKey]['quantity'] += (float)$item->quantity;
            }
        }

        // Sort alphabetically
        usort($groupedItems, function ($a, $b) {
            return $a['name'] <=> $b['name'];
        });

        return view('livewire.admin.menu-polda.material-shipment.admin-menu-polda-material-shipment-print', [
            'shipmentDetails' => $groupedItems
        ])->layout('components.layouts.print', ['title' => 'Surat Pengiriman Material - ' . $this->shipment->code]);
    }

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
}
