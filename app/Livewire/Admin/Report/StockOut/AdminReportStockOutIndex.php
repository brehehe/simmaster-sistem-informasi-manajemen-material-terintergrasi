<?php

namespace App\Livewire\Admin\Report\StockOut;

use Livewire\Component;
use Livewire\WithPagination;

class AdminReportStockOutIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    // Dummy data untuk barang keluar - Produk Polda Jatim ke kota-kota Jatim
    public function getStockOutsProperty()
    {
        $stockOuts = collect([
            [
                'id' => 1,
                'no_dokumen' => 'OUT-2024-001',
                'tanggal' => '2024-12-01',
                'kode_barang' => 'SIMCARD-001',
                'nama_barang' => 'SIM CARD',
                'jumlah' => 250,
                'satuan' => 'Lembar',
                'tujuan' => 'Polres Surabaya',
                'pemohon' => 'AKP Rudi Hartono',
                'status' => 'Disetujui',
            ],
            [
                'id' => 2,
                'no_dokumen' => 'OUT-2024-002',
                'tanggal' => '2024-12-02',
                'kode_barang' => 'STNK-001',
                'nama_barang' => 'STNK',
                'jumlah' => 300,
                'satuan' => 'Lembar',
                'tujuan' => 'Polres Malang',
                'pemohon' => 'Kompol Susilo',
                'status' => 'Disetujui',
            ],
            [
                'id' => 3,
                'no_dokumen' => 'OUT-2024-003',
                'tanggal' => '2024-12-03',
                'kode_barang' => 'BPKB-001',
                'nama_barang' => 'BPKB',
                'jumlah' => 400,
                'satuan' => 'Lembar',
                'tujuan' => 'Polres Sidoarjo',
                'pemohon' => 'AKBP Wahyu',
                'status' => 'Disetujui',
            ],
            [
                'id' => 4,
                'no_dokumen' => 'OUT-2024-004',
                'tanggal' => '2024-12-04',
                'kode_barang' => 'EBPKB-001',
                'nama_barang' => 'E-BPKB',
                'jumlah' => 350,
                'satuan' => 'Lembar',
                'tujuan' => 'Polres Gresik',
                'pemohon' => 'AKP Bambang',
                'status' => 'Disetujui',
            ],
            [
                'id' => 5,
                'no_dokumen' => 'OUT-2024-005',
                'tanggal' => '2024-12-05',
                'kode_barang' => 'TNKB-REG-001',
                'nama_barang' => 'TNKB REG',
                'jumlah' => 800,
                'satuan' => 'Pasang',
                'tujuan' => 'Polres Kediri',
                'pemohon' => 'Kompol Teguh',
                'status' => 'Disetujui',
            ],
            [
                'id' => 6,
                'no_dokumen' => 'OUT-2024-006',
                'tanggal' => '2024-12-05',
                'kode_barang' => 'TNKB-LIS-001',
                'nama_barang' => 'TNKB LISTRIK',
                'jumlah' => 150,
                'satuan' => 'Pasang',
                'tujuan' => 'Polres Mojokerto',
                'pemohon' => 'AKP Sudirman',
                'status' => 'Disetujui',
            ],
            [
                'id' => 7,
                'no_dokumen' => 'OUT-2024-007',
                'tanggal' => '2024-12-06',
                'kode_barang' => 'STCK-001',
                'nama_barang' => 'STCK',
                'jumlah' => 200,
                'satuan' => 'Lembar',
                'tujuan' => 'Polres Jember',
                'pemohon' => 'Kompol Hendri',
                'status' => 'Disetujui',
            ],
            [
                'id' => 8,
                'no_dokumen' => 'OUT-2024-008',
                'tanggal' => '2024-12-07',
                'kode_barang' => 'MUT-001',
                'nama_barang' => 'MUTASI',
                'jumlah' => 100,
                'satuan' => 'Lembar',
                'tujuan' => 'Polres Banyuwangi',
                'pemohon' => 'AKP Rosyid',
                'status' => 'Pending',
            ],
            [
                'id' => 9,
                'no_dokumen' => 'OUT-2024-009',
                'tanggal' => '2024-12-08',
                'kode_barang' => 'NRKB-NP-001',
                'nama_barang' => 'NRKB NOPIL',
                'jumlah' => 50,
                'satuan' => 'Pasang',
                'tujuan' => 'Polres Madiun',
                'pemohon' => 'AKBP Mulyadi',
                'status' => 'Pending',
            ],
            [
                'id' => 10,
                'no_dokumen' => 'OUT-2024-010',
                'tanggal' => '2024-12-09',
                'kode_barang' => 'NRKB-NPL-001',
                'nama_barang' => 'NRKB NOPIL LISTRIK',
                'jumlah' => 30,
                'satuan' => 'Pasang',
                'tujuan' => 'Polres Blitar',
                'pemohon' => 'Kompol Andi',
                'status' => 'Ditolak',
            ],
        ]);

        // Filter by search
        if ($this->search) {
            $stockOuts = $stockOuts->filter(function ($item) {
                return str_contains(strtolower($item['nama_barang']), strtolower($this->search)) ||
                       str_contains(strtolower($item['no_dokumen']), strtolower($this->search)) ||
                       str_contains(strtolower($item['tujuan']), strtolower($this->search));
            });
        }

        return $stockOuts->values();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.stock-out.admin-report-stock-out-index')
            ->layout('components.layouts.main.app');
    }
}
