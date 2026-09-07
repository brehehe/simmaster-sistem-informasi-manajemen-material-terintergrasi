<?php

namespace App\Livewire\Warehouse;

use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class WarehouseScanIndex extends Component
{
    use WithFileUploads;

    // Scan / Search
    public string $scanInputCode = '';
    public ?MaterialShipment $scannedShipment = null;

    // Form Identitas Pengambil
    public string $pickerName     = '';
    public string $pickerRank     = '';
    public string $pickerPosition = '';

    // TTD & Foto
    public string $pickerSignature = ''; // base64 PNG dari canvas
    public $pickerPhoto = null;          // upload file
    public ?string $pickerPhotoPreview = null;

    // UI state
    public string $step = 'scan'; // scan | form | success
    public bool $submitting = false;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user && !$user->hasRole(['Admin', 'Polda', 'Warehouse'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman Scan QR Warehouse.');
        }

        // Step awal selalu scan
        $this->step = 'scan';
    }

    public function processScanQr(): void
    {
        $this->resetErrorBag();
        $code = trim($this->scanInputCode);

        if (empty($code)) {
            $this->addError('scanInputCode', 'Silakan masukkan atau scan kode SPPM.');
            return;
        }

        $shipment = MaterialShipment::with([
            'receiverPoliceStation',
            'senderRegionalPolice',
            'materialShipmentDetails.type',
            'materialShipmentDetails.typeDetail',
            'materialShipmentDetails.stockDetail.rack',
        ])->where(function ($q) use ($code) {
            $q->where('code', 'ilike', '%' . $code . '%')
              ->orWhere('code', 'ILIKE', '%' . $code . '%');
            if (\Illuminate\Support\Str::isUuid($code)) {
                $q->orWhere('id', $code);
            }
        })->where('is_active', true)->first();

        if (!$shipment) {
            $this->addError('scanInputCode', "Data SPPM dengan kode '{$code}' tidak ditemukan.");
            $this->scannedShipment = null;
            return;
        }

        if ($shipment->status !== 'draft') {
            $this->addError('scanInputCode', "SPPM {$shipment->code} sudah berstatus " . strtoupper($shipment->status) . ". Hanya SPPM draft yang bisa diproses.");
            return;
        }

        $this->scannedShipment = $shipment;
        $this->step = 'form';
    }

    public function resetScan(): void
    {
        $this->scanInputCode    = '';
        $this->scannedShipment  = null;
        $this->pickerName       = '';
        $this->pickerRank       = '';
        $this->pickerPosition   = '';
        $this->pickerSignature  = '';
        $this->pickerPhoto      = null;
        $this->pickerPhotoPreview = null;
        $this->step = 'scan';
        $this->resetErrorBag();
    }

    public function submitPicking(): void
    {
        if (!$this->scannedShipment) return;

        $this->validate([
            'pickerName'      => 'required|string|max:200',
            'pickerRank'      => 'required|string|max:100',
            'pickerPosition'  => 'required|string|max:200',
            'pickerSignature' => 'required|string',
            'pickerPhoto'     => 'nullable|file|image|max:5120', // max 5MB
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

            $this->step = 'success';
        } catch (\Exception $e) {
            $this->addError('pickerName', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.warehouse.warehouse-scan-index')
            ->layout('components.layouts.main.app');
    }
}
