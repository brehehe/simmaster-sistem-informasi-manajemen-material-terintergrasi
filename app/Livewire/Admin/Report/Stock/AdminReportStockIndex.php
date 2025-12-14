<?php

namespace App\Livewire\Admin\Report\Stock;

use Livewire\Component;
use Livewire\WithPagination;

class AdminReportStockIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterCategory = '';

    // Dummy data untuk stok barang - Produk Polda Jatim
    public function getStocksProperty()
    {
        $stocks = collect([
            [
                'id' => 1,
                'kode_barang' => 'SIMCARD-001',
                'nama_barang' => 'SIM CARD',
                'kategori' => 'Dokumen Identitas',
                'satuan' => 'Lembar',
                'stok_awal' => 5000,
                'stok_masuk' => 1500,
                'stok_keluar' => 2000,
                'stok_akhir' => 4500,
                'lokasi_rak' => 'RAK-A01',
                'kondisi' => 'Baik',
            ],
            [
                'id' => 2,
                'kode_barang' => 'STNK-001',
                'nama_barang' => 'STNK',
                'kategori' => 'Dokumen Kendaraan',
                'satuan' => 'Lembar',
                'stok_awal' => 8000,
                'stok_masuk' => 2500,
                'stok_keluar' => 3000,
                'stok_akhir' => 7500,
                'lokasi_rak' => 'RAK-A02',
                'kondisi' => 'Baik',
            ],
            [
                'id' => 3,
                'kode_barang' => 'STCK-001',
                'nama_barang' => 'STCK',
                'kategori' => 'Dokumen Kendaraan',
                'satuan' => 'Lembar',
                'stok_awal' => 3000,
                'stok_masuk' => 1000,
                'stok_keluar' => 800,
                'stok_akhir' => 3200,
                'lokasi_rak' => 'RAK-A03',
                'kondisi' => 'Baik',
            ],
            [
                'id' => 4,
                'kode_barang' => 'EBPKB-001',
                'nama_barang' => 'E-BPKB',
                'kategori' => 'Dokumen Kendaraan',
                'satuan' => 'Lembar',
                'stok_awal' => 10000,
                'stok_masuk' => 3000,
                'stok_keluar' => 4000,
                'stok_akhir' => 9000,
                'lokasi_rak' => 'RAK-B01',
                'kondisi' => 'Baik',
            ],
            [
                'id' => 5,
                'kode_barang' => 'BPKB-001',
                'nama_barang' => 'BPKB',
                'kategori' => 'Dokumen Kendaraan',
                'satuan' => 'Lembar',
                'stok_awal' => 12000,
                'stok_masuk' => 4000,
                'stok_keluar' => 5000,
                'stok_akhir' => 11000,
                'lokasi_rak' => 'RAK-B02',
                'kondisi' => 'Baik',
            ],
            [
                'id' => 6,
                'kode_barang' => 'MUT-001',
                'nama_barang' => 'MUTASI',
                'kategori' => 'Dokumen Kendaraan',
                'satuan' => 'Lembar',
                'stok_awal' => 2000,
                'stok_masuk' => 500,
                'stok_keluar' => 600,
                'stok_akhir' => 1900,
                'lokasi_rak' => 'RAK-B03',
                'kondisi' => 'Baik',
            ],
            [
                'id' => 7,
                'kode_barang' => 'TNKB-REG-001',
                'nama_barang' => 'TNKB REG',
                'kategori' => 'Plat Nomor',
                'satuan' => 'Pasang',
                'stok_awal' => 15000,
                'stok_masuk' => 5000,
                'stok_keluar' => 6000,
                'stok_akhir' => 14000,
                'lokasi_rak' => 'RAK-C01',
                'kondisi' => 'Baik',
            ],
            [
                'id' => 8,
                'kode_barang' => 'TNKB-LIS-001',
                'nama_barang' => 'TNKB LISTRIK',
                'kategori' => 'Plat Nomor',
                'satuan' => 'Pasang',
                'stok_awal' => 3000,
                'stok_masuk' => 1000,
                'stok_keluar' => 800,
                'stok_akhir' => 3200,
                'lokasi_rak' => 'RAK-C02',
                'kondisi' => 'Baik',
            ],
            [
                'id' => 9,
                'kode_barang' => 'NRKB-NP-001',
                'nama_barang' => 'NRKB NOPIL',
                'kategori' => 'Plat Nomor',
                'satuan' => 'Pasang',
                'stok_awal' => 500,
                'stok_masuk' => 200,
                'stok_keluar' => 180,
                'stok_akhir' => 520,
                'lokasi_rak' => 'RAK-C03',
                'kondisi' => 'Baik',
            ],
            [
                'id' => 10,
                'kode_barang' => 'NRKB-NPL-001',
                'nama_barang' => 'NRKB NOPIL LISTRIK',
                'kategori' => 'Plat Nomor',
                'satuan' => 'Pasang',
                'stok_awal' => 200,
                'stok_masuk' => 100,
                'stok_keluar' => 80,
                'stok_akhir' => 220,
                'lokasi_rak' => 'RAK-C04',
                'kondisi' => 'Baik',
            ],
        ]);

        // Filter by search
        if ($this->search) {
            $stocks = $stocks->filter(function ($item) {
                return str_contains(strtolower($item['nama_barang']), strtolower($this->search)) ||
                       str_contains(strtolower($item['kode_barang']), strtolower($this->search));
            });
        }

        // Filter by category
        if ($this->filterCategory) {
            $stocks = $stocks->filter(function ($item) {
                return $item['kategori'] === $this->filterCategory;
            });
        }

        return $stocks->values();
    }

    public function getCategoriesProperty()
    {
        return [
            'Dokumen Identitas',
            'Dokumen Kendaraan',
            'Plat Nomor',
        ];
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterCategory()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.report.stock.admin-report-stock-index')
            ->layout('components.layouts.main.app');
    }
}
