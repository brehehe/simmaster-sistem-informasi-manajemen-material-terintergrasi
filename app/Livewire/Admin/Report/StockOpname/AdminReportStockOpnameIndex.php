<?php

namespace App\Livewire\Admin\Report\StockOpname;

use Livewire\Component;
use Livewire\WithPagination;

class AdminReportStockOpnameIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterStatus = '';

    // Dummy data untuk stock opname - Produk Polda Jatim
    public function getStockOpnamesProperty()
    {
        $stockOpnames = collect([
            [
                'id' => 1,
                'no_opname' => 'OPN-2024-001',
                'tanggal' => '2024-12-01',
                'kode_barang' => 'SIMCARD-001',
                'nama_barang' => 'SIM CARD',
                'stok_sistem' => 4500,
                'stok_fisik' => 4498,
                'selisih' => -2,
                'satuan' => 'Lembar',
                'keterangan' => 'Selisih minor - kerusakan cetak',
                'petugas' => 'Aipda Ahmad',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 2,
                'no_opname' => 'OPN-2024-002',
                'tanggal' => '2024-12-01',
                'kode_barang' => 'STNK-001',
                'nama_barang' => 'STNK',
                'stok_sistem' => 7500,
                'stok_fisik' => 7500,
                'selisih' => 0,
                'satuan' => 'Lembar',
                'keterangan' => 'Sesuai',
                'petugas' => 'Aipda Ahmad',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 3,
                'no_opname' => 'OPN-2024-003',
                'tanggal' => '2024-12-01',
                'kode_barang' => 'STCK-001',
                'nama_barang' => 'STCK',
                'stok_sistem' => 3200,
                'stok_fisik' => 3195,
                'selisih' => -5,
                'satuan' => 'Lembar',
                'keterangan' => 'Perlu investigasi selisih',
                'petugas' => 'Brigadir Budi',
                'status' => 'Dalam Review',
            ],
            [
                'id' => 4,
                'no_opname' => 'OPN-2024-004',
                'tanggal' => '2024-12-01',
                'kode_barang' => 'EBPKB-001',
                'nama_barang' => 'E-BPKB',
                'stok_sistem' => 9000,
                'stok_fisik' => 9000,
                'selisih' => 0,
                'satuan' => 'Lembar',
                'keterangan' => 'Sesuai - kondisi baik',
                'petugas' => 'Iptu Cahyo',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 5,
                'no_opname' => 'OPN-2024-005',
                'tanggal' => '2024-12-02',
                'kode_barang' => 'BPKB-001',
                'nama_barang' => 'BPKB',
                'stok_sistem' => 11000,
                'stok_fisik' => 10998,
                'selisih' => -2,
                'satuan' => 'Lembar',
                'keterangan' => '2 lembar rusak cetak',
                'petugas' => 'Aipda Dedi',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 6,
                'no_opname' => 'OPN-2024-006',
                'tanggal' => '2024-12-02',
                'kode_barang' => 'MUT-001',
                'nama_barang' => 'MUTASI',
                'stok_sistem' => 1900,
                'stok_fisik' => 1900,
                'selisih' => 0,
                'satuan' => 'Lembar',
                'keterangan' => 'Sesuai',
                'petugas' => 'Brigadir Eko',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 7,
                'no_opname' => 'OPN-2024-007',
                'tanggal' => '2024-12-02',
                'kode_barang' => 'TNKB-REG-001',
                'nama_barang' => 'TNKB REG',
                'stok_sistem' => 14000,
                'stok_fisik' => 13998,
                'selisih' => -2,
                'satuan' => 'Pasang',
                'keterangan' => '2 pasang dalam perbaikan',
                'petugas' => 'Iptu Faisal',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 8,
                'no_opname' => 'OPN-2024-008',
                'tanggal' => '2024-12-03',
                'kode_barang' => 'TNKB-LIS-001',
                'nama_barang' => 'TNKB LISTRIK',
                'stok_sistem' => 3200,
                'stok_fisik' => 3205,
                'selisih' => 5,
                'satuan' => 'Pasang',
                'keterangan' => 'Lebih - perlu konfirmasi',
                'petugas' => 'Aipda Gunawan',
                'status' => 'Dalam Review',
            ],
            [
                'id' => 9,
                'no_opname' => 'OPN-2024-009',
                'tanggal' => '2024-12-03',
                'kode_barang' => 'NRKB-NP-001',
                'nama_barang' => 'NRKB NOPIL',
                'stok_sistem' => 520,
                'stok_fisik' => 520,
                'selisih' => 0,
                'satuan' => 'Pasang',
                'keterangan' => 'Sesuai',
                'petugas' => 'Brigadir Hendra',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 10,
                'no_opname' => 'OPN-2024-010',
                'tanggal' => '2024-12-03',
                'kode_barang' => 'NRKB-NPL-001',
                'nama_barang' => 'NRKB NOPIL LISTRIK',
                'stok_sistem' => 220,
                'stok_fisik' => 218,
                'selisih' => -2,
                'satuan' => 'Pasang',
                'keterangan' => '2 pasang cacat produksi',
                'petugas' => 'Aiptu Irwan',
                'status' => 'Pending',
            ],
        ]);

        // Filter by search
        if ($this->search) {
            $stockOpnames = $stockOpnames->filter(function ($item) {
                return str_contains(strtolower($item['nama_barang']), strtolower($this->search)) ||
                       str_contains(strtolower($item['no_opname']), strtolower($this->search)) ||
                       str_contains(strtolower($item['kode_barang']), strtolower($this->search));
            });
        }

        // Filter by status
        if ($this->filterStatus) {
            $stockOpnames = $stockOpnames->filter(function ($item) {
                return $item['status'] === $this->filterStatus;
            });
        }

        return $stockOpnames->values();
    }

    public function getStatusesProperty()
    {
        return [
            'Terverifikasi',
            'Dalam Review',
            'Pending',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.stock-opname.admin-report-stock-opname-index')
            ->layout('components.layouts.main.app');
    }
}
