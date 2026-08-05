<?php

namespace App\Livewire\Admin\MenuPolda\MaterialShipment;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use App\Models\Police\PoliceStation;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Police\RegionalPolice;
use Illuminate\Support\Facades\Storage;

class AdminMenuPoldaMaterialShipmentIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $statusFilter = '';
    public string $polresFilter = '';
    public string $poldaFilter = '';
    public int $perPage = 10;
    public bool $showDeleteModal = false;
    public bool $showDetailModal = false;
    public bool $showScanQrModal = false;
    public bool $showPickingDetailModal = false;
    public ?string $shipmentId = null;
    public $selectedShipment = null;
    public string $scanInputCode = '';
    public $scannedShipment = null;

    // Picking form fields (TTD, Foto, Identitas)
    public string $pickerName     = '';
    public string $pickerRank     = '';
    public string $pickerPosition = '';
    public string $pickerSignature = ''; // base64 PNG
    public $pickerPhoto = null;

    public function toJSON()
    {
        return [];
    }

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function render()
    {
        $user = auth()->user();

        $query = MaterialShipment::with([
            'senderRegionalPolice', 
            'receiverPoliceStation', 
            'materialShipmentDetails',
            'materialShipmentDetails.stockDetail',
            'materialShipmentDetails.stockDetail.service',
            'materialShipmentDetails.stockDetail.serviceDetail',
            'materialShipmentDetails.type',
            'materialShipmentDetails.typeDetail'
        ])
            ->where('is_active', true);

        // Role-based: Polda only sees their own shipments
        if ($user->hasRole('Polda')) {
            $query->where('sender_regional_police_id', $user->regional_police_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Polres filter
        if ($this->polresFilter) {
            $query->where('receiver_police_station_id', $this->polresFilter);
        }

        // Date filtering
        if ($this->startDate) {
            $query->whereDate('shipment_date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('shipment_date', '<=', $this->endDate);
        }

        $shipments = $query->latest('shipment_date')->paginate($this->perPage);

        // Get police stations for filter
        $policeStations = PoliceStation::where('is_active', true)->orderBy('name')->get();

        $regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.menu-polda.material-shipment.admin-menu-polda-material-shipment-index', [
            'shipments' => $shipments,
            'regionalPolices' => $regionalPolices,
            'policeStations' => $policeStations,
        ])->layout('components.layouts.main.app');
    }

    public function openDeleteModal($id)
    {
        $this->shipmentId = $id;
        $this->showDeleteModal = true;
    }

    public function closeModal()
    {
        $this->showDeleteModal = false;
        $this->shipmentId = null;
    }

    public function delete()
    {
        if ($this->shipmentId) {
            $shipment = MaterialShipment::find($this->shipmentId);
            if ($shipment && $shipment->status === 'draft') {
                $shipment->delete();
                session()->flash('success', 'Pengiriman berhasil dihapus.');
            } else {
                session()->flash('error', 'Hanya pengiriman dengan status draft yang bisa dihapus.');
            }
        }
        $this->closeModal();
    }

    public function viewDetail($id)
    {
        $this->shipmentId = $id;
        $this->selectedShipment = MaterialShipment::with([
            'senderRegionalPolice',
            'receiverPoliceStation',
            'materialShipmentDetails.stockDetail',
            'materialShipmentDetails.stockDetail.service',
            'materialShipmentDetails.stockDetail.serviceDetail',
            'materialShipmentDetails.type',
            'materialShipmentDetails.typeDetail'
        ])->find($id);
        $this->showDetailModal = true;
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedShipment = null;
        $this->shipmentId = null;
    }

    public function openScanQrModal(?string $id = null): void
    {
        $this->shipmentId = $id;
        $this->scanInputCode = '';
        $this->scannedShipment = null;
        $this->resetPickingForm();

        if ($id) {
            $this->scannedShipment = MaterialShipment::with([
                'receiverPoliceStation',
                'materialShipmentDetails.type',
                'materialShipmentDetails.typeDetail',
                'materialShipmentDetails.stockDetail.rack'
            ])->find($id);
            if ($this->scannedShipment) {
                $this->scanInputCode = $this->scannedShipment->code;
            }
        }
        $this->showScanQrModal = true;
    }

    public function closeScanQrModal(): void
    {
        $this->showScanQrModal = false;
        $this->scanInputCode = '';
        $this->scannedShipment = null;
        $this->shipmentId = null;
        $this->resetPickingForm();
    }

    private function resetPickingForm(): void
    {
        $this->pickerName      = '';
        $this->pickerRank      = '';
        $this->pickerPosition  = '';
        $this->pickerSignature = '';
        $this->pickerPhoto     = null;
    }

    public function processScanQr(): void
    {
        if (empty(trim($this->scanInputCode))) {
            session()->flash('error', 'Silakan masukkan atau scan kode QR SPPM.');
            return;
        }

        $code = trim($this->scanInputCode);
        $shipment = MaterialShipment::with([
            'receiverPoliceStation',
            'materialShipmentDetails.type',
            'materialShipmentDetails.typeDetail',
            'materialShipmentDetails.stockDetail.rack'
        ])->where(function ($q) use ($code) {
            $q->where('code', 'ilike', '%' . $code . '%');
            if (\Illuminate\Support\Str::isUuid($code)) {
                $q->orWhere('id', $code);
            }
        })->first();

        if (!$shipment) {
            session()->flash('error', "Data pengiriman dengan kode '{$code}' tidak ditemukan.");
            $this->scannedShipment = null;
            return;
        }

        $this->scannedShipment = $shipment;
        session()->flash('success', "QR Code Valid! Transaksi SPPM {$shipment->code} ditemukan.");
    }

    public function confirmWarehousePicking(): void
    {
        if (!$this->scannedShipment) return;

        $this->validate([
            'pickerName'      => 'required|string|max:200',
            'pickerRank'      => 'required|string|max:100',
            'pickerPosition'  => 'required|string|max:200',
            'pickerSignature' => 'required|string',
            'pickerPhoto'     => 'nullable|file|image|max:5120',
        ], [
            'pickerName.required'      => 'Nama pengambil wajib diisi.',
            'pickerRank.required'      => 'Pangkat wajib diisi.',
            'pickerPosition.required'  => 'Jabatan wajib diisi.',
            'pickerSignature.required' => 'Tanda tangan digital wajib dibuat.',
        ]);

        try {
            $photoPath = null;
            if ($this->pickerPhoto) {
                $photoPath = $this->pickerPhoto->store('shipment-photos', 'public');
            }

            if ($this->scannedShipment->status === 'draft') {
                $this->scannedShipment->update([
                    'status'           => 'shipped',
                    'shipped_at'       => now(),
                    'picker_name'      => $this->pickerName,
                    'picker_rank'      => $this->pickerRank,
                    'picker_position'  => $this->pickerPosition,
                    'picker_signature' => $this->pickerSignature,
                    'picker_photo'     => $photoPath,
                    'picked_at'        => now(),
                ]);
                session()->flash('success', "Serah terima material SPPM {$this->scannedShipment->code} berhasil! Status: Terkirim.");
            } else {
                session()->flash('info', "SPPM {$this->scannedShipment->code} sudah diverifikasi sebelumnya.");
            }
            $this->closeScanQrModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memproses verifikasi: ' . $e->getMessage());
        }
    }

    public function openPickingDetailModal(string $id): void
    {
        $this->shipmentId = $id;
        $this->selectedShipment = MaterialShipment::with([
            'receiverPoliceStation',
            'senderRegionalPolice',
            'materialShipmentDetails.type',
            'materialShipmentDetails.typeDetail',
            'materialShipmentDetails.stockDetail.rack'
        ])->find($id);
        $this->showPickingDetailModal = true;
    }

    public function closePickingDetailModal(): void
    {
        $this->showPickingDetailModal = false;
        $this->selectedShipment = null;
        $this->shipmentId = null;
    }
}
