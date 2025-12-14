<?php

namespace App\Livewire\Admin\Report\Delivery\Detail;

use Livewire\Component;

class AdminReportDeliveryDetailIndex extends Component
{
    public $deliveryId;

    // Dummy detail pengiriman
    public function getDeliveryProperty()
    {
        return [
            'id' => 1,
            'no_surat_jalan' => 'SJ-2024-001',
            'tanggal_kirim' => '2024-12-01',
            'tanggal_terima' => '2024-12-01',
            'tujuan' => 'Polres Jakarta Selatan',
            'alamat' => 'Jl. Raya Kebayoran Baru No. 1, Jakarta Selatan 12140',
            'kurir' => 'Bripka Andi Setiawan',
            'kendaraan' => 'B 1234 POL',
            'jenis_kendaraan' => 'Toyota Hilux',
            'penerima' => 'AKP Rudi Hartono',
            'telepon_penerima' => '0812-3456-7890',
            'catatan' => 'Barang diterima dalam kondisi baik. Sesuai dengan dokumen.',
            'status' => 'Terkirim',
        ];
    }

    // Dummy detail barang dalam pengiriman
    public function getDeliveryItemsProperty()
    {
        return collect([
            [
                'id' => 1,
                'kode_barang' => 'BRG-001',
                'nama_barang' => 'Seragam Dinas Harian Polri',
                'satuan' => 'Set',
                'jumlah_kirim' => 50,
                'jumlah_terima' => 50,
                'kondisi' => 'Baik',
            ],
            [
                'id' => 2,
                'kode_barang' => 'BRG-002',
                'nama_barang' => 'Sepatu PDL',
                'satuan' => 'Pasang',
                'jumlah_kirim' => 30,
                'jumlah_terima' => 30,
                'kondisi' => 'Baik',
            ],
            [
                'id' => 3,
                'kode_barang' => 'BRG-003',
                'nama_barang' => 'Topi Polisi',
                'satuan' => 'Pcs',
                'jumlah_kirim' => 40,
                'jumlah_terima' => 40,
                'kondisi' => 'Baik',
            ],
            [
                'id' => 4,
                'kode_barang' => 'BRG-009',
                'nama_barang' => 'Helm Polisi',
                'satuan' => 'Pcs',
                'jumlah_kirim' => 20,
                'jumlah_terima' => 20,
                'kondisi' => 'Baik',
            ],
            [
                'id' => 5,
                'kode_barang' => 'BRG-010',
                'nama_barang' => 'Ikat Pinggang P3',
                'satuan' => 'Pcs',
                'jumlah_kirim' => 10,
                'jumlah_terima' => 10,
                'kondisi' => 'Baik',
            ],
        ]);
    }

    public function mount($id = null)
    {
        $this->deliveryId = $id ?? 1;
    }

    public function render()
    {
        return view('livewire.admin.report.delivery.detail.admin-report-delivery-detail-index')
            ->layout('components.layouts.main.app');
    }
}
