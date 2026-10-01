<?php

namespace App\Livewire\Admin\MenuPolda\MaterialShipment;

use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use Livewire\Component;

class AdminMenuPoldaMaterialShipmentPrint extends Component
{
    #[\Livewire\Attributes\Locked]
    public $shipmentId;

    public $shipment;

    public string $mode = 'qr';

    public function mount($id)
    {
        $this->shipmentId = $id;
        $this->mode = request()->query('mode', 'qr');
        $this->shipment = MaterialShipment::with([
            'senderRegionalPolice',
            'receiverPoliceStation',
            'materialShipmentDetails.type',
            'materialShipmentDetails.typeDetail',
            'materialShipmentDetails.stockDetail.service',
            'materialShipmentDetails.stockDetail.serviceDetail',
        ])->findOrFail($id);
        $this->authorize('view', $this->shipment);
    }

    public function render()
    {
        return view('livewire.admin.menu-polda.material-shipment.admin-menu-polda-material-shipment-print', [
            'formatter' => $this, 'shipmentDetails' => $this->shipment->materialShipmentDetails,
        ])->layout('components.layouts.print', ['title' => 'Surat Pengiriman Material - '.$this->shipment->code]);
    }

    public function exportPdf()
    {
        $shipment = MaterialShipment::with(['senderRegionalPolice', 'receiverPoliceStation', 'materialShipmentDetails.type', 'materialShipmentDetails.typeDetail', 'materialShipmentDetails.stockDetail.service', 'materialShipmentDetails.stockDetail.serviceDetail'])->findOrFail($this->shipmentId);
        $this->authorize('view', $shipment);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.admin.menu-polda.material-shipment.admin-menu-polda-material-shipment-print', [
            'shipment' => $shipment, 'shipmentDetails' => $shipment->materialShipmentDetails, 'formatter' => $this,
            'mode' => $this->mode, 'isPdf' => true,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(fn () => print ($pdf->output()), str_replace('/', '_', $shipment->code).'.pdf');
    }

    public function terbilang($angka)
    {
        $angka = abs(floor($angka));
        $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        $terbilang = '';
        if ($angka < 12) {
            $terbilang = ' '.$baca[$angka];
        } elseif ($angka < 20) {
            $terbilang = $this->terbilang($angka - 10).' Belas';
        } elseif ($angka < 100) {
            $terbilang = $this->terbilang($angka / 10).' Puluh'.$this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            $terbilang = ' Seratus'.$this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            $terbilang = $this->terbilang($angka / 100).' Ratus'.$this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            $terbilang = ' Seribu'.$this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            $terbilang = $this->terbilang($angka / 1000).' Ribu'.$this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            $terbilang = $this->terbilang($angka / 1000000).' Juta'.$this->terbilang($angka % 1000000);
        }

        return trim($terbilang);
    }
}
