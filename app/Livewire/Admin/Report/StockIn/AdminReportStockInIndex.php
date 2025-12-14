<?php

namespace App\Livewire\Admin\Report\StockIn;

use Livewire\Component;
use Livewire\WithPagination;

class AdminReportStockInIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterDateFrom = '';
    public $filterDateTo = '';

    // Dummy data untuk barang masuk - Produk Polda Jatim
    public function getStockInsProperty()
    {
        $stockIns = collect([
            [
                'id' => 1,
                'no_dokumen' => 'IN-2024-001',
                'tanggal' => '2024-12-01',
                'kode_barang' => 'SIMCARD-001',
                'nama_barang' => 'SIM CARD',
                'jumlah' => 500,
                'satuan' => 'Lembar',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Bripka Ahmad',
                'status' => 'Diterima',
            ],
            [
                'id' => 2,
                'no_dokumen' => 'IN-2024-002',
                'tanggal' => '2024-12-02',
                'kode_barang' => 'STNK-001',
                'nama_barang' => 'STNK',
                'jumlah' => 800,
                'satuan' => 'Lembar',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Aipda Budi',
                'status' => 'Diterima',
            ],
            [
                'id' => 3,
                'no_dokumen' => 'IN-2024-003',
                'tanggal' => '2024-12-03',
                'kode_barang' => 'BPKB-001',
                'nama_barang' => 'BPKB',
                'jumlah' => 1000,
                'satuan' => 'Lembar',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Iptu Cahyo',
                'status' => 'Diterima',
            ],
            [
                'id' => 4,
                'no_dokumen' => 'IN-2024-004',
                'tanggal' => '2024-12-04',
                'kode_barang' => 'EBPKB-001',
                'nama_barang' => 'E-BPKB',
                'jumlah' => 750,
                'satuan' => 'Lembar',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Aipda Dedi',
                'status' => 'Diterima',
            ],
            [
                'id' => 5,
                'no_dokumen' => 'IN-2024-005',
                'tanggal' => '2024-12-05',
                'kode_barang' => 'TNKB-REG-001',
                'nama_barang' => 'TNKB REG',
                'jumlah' => 2000,
                'satuan' => 'Pasang',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Bripka Eko',
                'status' => 'Diterima',
            ],
            [
                'id' => 6,
                'no_dokumen' => 'IN-2024-006',
                'tanggal' => '2024-12-05',
                'kode_barang' => 'TNKB-LIS-001',
                'nama_barang' => 'TNKB LISTRIK',
                'jumlah' => 500,
                'satuan' => 'Pasang',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Aiptu Faisal',
                'status' => 'Diterima',
            ],
            [
                'id' => 7,
                'no_dokumen' => 'IN-2024-007',
                'tanggal' => '2024-12-06',
                'kode_barang' => 'STCK-001',
                'nama_barang' => 'STCK',
                'jumlah' => 400,
                'satuan' => 'Lembar',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Bripka Gunawan',
                'status' => 'Diterima',
            ],
            [
                'id' => 8,
                'no_dokumen' => 'IN-2024-008',
                'tanggal' => '2024-12-07',
                'kode_barang' => 'MUT-001',
                'nama_barang' => 'MUTASI',
                'jumlah' => 200,
                'satuan' => 'Lembar',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Aipda Hendra',
                'status' => 'Diterima',
            ],
            [
                'id' => 9,
                'no_dokumen' => 'IN-2024-009',
                'tanggal' => '2024-12-08',
                'kode_barang' => 'NRKB-NP-001',
                'nama_barang' => 'NRKB NOPIL',
                'jumlah' => 100,
                'satuan' => 'Pasang',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Brigadir Irwan',
                'status' => 'Pending',
            ],
            [
                'id' => 10,
                'no_dokumen' => 'IN-2024-010',
                'tanggal' => '2024-12-09',
                'kode_barang' => 'NRKB-NPL-001',
                'nama_barang' => 'NRKB NOPIL LISTRIK',
                'jumlah' => 50,
                'satuan' => 'Pasang',
                'asal' => 'Gudang Pusat Korlantas',
                'penerima' => 'Aiptu Joko',
                'status' => 'Pending',
            ],
        ]);

        // Filter by search
        if ($this->search) {
            $stockIns = $stockIns->filter(function ($item) {
                return str_contains(strtolower($item['nama_barang']), strtolower($this->search)) ||
                       str_contains(strtolower($item['no_dokumen']), strtolower($this->search)) ||
                       str_contains(strtolower($item['kode_barang']), strtolower($this->search));
            });
        }

        return $stockIns->values();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.stock-in.admin-report-stock-in-index')
            ->layout('components.layouts.main.app');
    }
}
