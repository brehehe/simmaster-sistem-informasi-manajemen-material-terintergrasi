<?php

namespace App\Livewire\Admin\Report\Reception\Detail;

use Livewire\Component;

class AdminReportReceptionDetailIndex extends Component
{
    public $receptionId;

    // Dummy detail penerimaan
    public function getReceptionProperty()
    {
        return [
            'id' => 1,
            'no_penerimaan' => 'REC-2024-001',
            'tanggal_terima' => '2024-12-01',
            'asal' => 'Gudang Pusat Mabes Polri',
            'no_surat_jalan' => 'SJ-MABES-2024-001',
            'penerima' => 'Iptu Ahmad Sudrajat',
            'jabatan' => 'Kasubag Logistik',
            'telepon' => '0812-9876-5432',
            'catatan' => 'Barang diterima lengkap sesuai dengan dokumen pengiriman. Kondisi barang baik tanpa kerusakan.',
            'kondisi' => 'Baik',
            'status' => 'Terverifikasi',
            'verifikator' => 'AKP Wahyu Pratama',
            'tanggal_verifikasi' => '2024-12-01',
        ];
    }

    // Dummy detail barang dalam penerimaan
    public function getReceptionItemsProperty()
    {
        return collect([
            [
                'id' => 1,
                'kode_barang' => 'BRG-001',
                'nama_barang' => 'Seragam Dinas Harian Polri',
                'satuan' => 'Set',
                'jumlah_dokumen' => 100,
                'jumlah_terima' => 100,
                'kondisi' => 'Baik',
                'lokasi_rak' => 'RAK-A01',
            ],
            [
                'id' => 2,
                'kode_barang' => 'BRG-002',
                'nama_barang' => 'Sepatu PDL',
                'satuan' => 'Pasang',
                'jumlah_dokumen' => 50,
                'jumlah_terima' => 50,
                'kondisi' => 'Baik',
                'lokasi_rak' => 'RAK-B02',
            ],
            [
                'id' => 3,
                'kode_barang' => 'BRG-003',
                'nama_barang' => 'Topi Polisi',
                'satuan' => 'Pcs',
                'jumlah_dokumen' => 75,
                'jumlah_terima' => 75,
                'kondisi' => 'Baik',
                'lokasi_rak' => 'RAK-A03',
            ],
            [
                'id' => 4,
                'kode_barang' => 'BRG-004',
                'nama_barang' => 'Rompi Anti Peluru',
                'satuan' => 'Pcs',
                'jumlah_dokumen' => 25,
                'jumlah_terima' => 25,
                'kondisi' => 'Baik',
                'lokasi_rak' => 'RAK-C01',
            ],
            [
                'id' => 5,
                'kode_barang' => 'BRG-005',
                'nama_barang' => 'Borgol',
                'satuan' => 'Pcs',
                'jumlah_dokumen' => 30,
                'jumlah_terima' => 30,
                'kondisi' => 'Baik',
                'lokasi_rak' => 'RAK-C02',
            ],
            [
                'id' => 6,
                'kode_barang' => 'BRG-007',
                'nama_barang' => 'HT (Handy Talky)',
                'satuan' => 'Unit',
                'jumlah_dokumen' => 20,
                'jumlah_terima' => 20,
                'kondisi' => 'Baik',
                'lokasi_rak' => 'RAK-D01',
            ],
            [
                'id' => 7,
                'kode_barang' => 'BRG-008',
                'nama_barang' => 'Senter Taktis',
                'satuan' => 'Pcs',
                'jumlah_dokumen' => 30,
                'jumlah_terima' => 30,
                'kondisi' => 'Baik',
                'lokasi_rak' => 'RAK-D02',
            ],
            [
                'id' => 8,
                'kode_barang' => 'BRG-009',
                'nama_barang' => 'Helm Polisi',
                'satuan' => 'Pcs',
                'jumlah_dokumen' => 20,
                'jumlah_terima' => 20,
                'kondisi' => 'Baik',
                'lokasi_rak' => 'RAK-A04',
            ],
        ]);
    }

    public function mount($id = null)
    {
        $this->receptionId = $id ?? 1;
    }

    public function render()
    {
        return view('livewire.admin.report.reception.detail.admin-report-reception-detail-index')
            ->layout('components.layouts.main.app');
    }
}
