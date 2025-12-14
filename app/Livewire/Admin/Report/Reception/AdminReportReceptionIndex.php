<?php

namespace App\Livewire\Admin\Report\Reception;

use Livewire\Component;
use Livewire\WithPagination;

class AdminReportReceptionIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterStatus = '';

    // Dummy data untuk penerimaan - Polda Jatim
    public function getReceptionsProperty()
    {
        $receptions = collect([
            [
                'id' => 1,
                'no_penerimaan' => 'REC-2024-001',
                'tanggal_terima' => '2024-12-01',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-001',
                'total_item' => 8,
                'total_unit' => 5000,
                'penerima' => 'Iptu Ahmad Sudrajat',
                'kondisi' => 'Baik',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 2,
                'no_penerimaan' => 'REC-2024-002',
                'tanggal_terima' => '2024-12-02',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-002',
                'total_item' => 5,
                'total_unit' => 3500,
                'penerima' => 'Aipda Budi Santoso',
                'kondisi' => 'Baik',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 3,
                'no_penerimaan' => 'REC-2024-003',
                'tanggal_terima' => '2024-12-03',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-003',
                'total_item' => 6,
                'total_unit' => 4000,
                'penerima' => 'Brigadir Cahyo Wibowo',
                'kondisi' => 'Baik',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 4,
                'no_penerimaan' => 'REC-2024-004',
                'tanggal_terima' => '2024-12-04',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-004',
                'total_item' => 4,
                'total_unit' => 2500,
                'penerima' => 'Iptu Dedi Kurniawan',
                'kondisi' => 'Baik',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 5,
                'no_penerimaan' => 'REC-2024-005',
                'tanggal_terima' => '2024-12-05',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-005',
                'total_item' => 7,
                'total_unit' => 4500,
                'penerima' => 'Aiptu Eko Prasetyo',
                'kondisi' => 'Baik',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 6,
                'no_penerimaan' => 'REC-2024-006',
                'tanggal_terima' => '2024-12-06',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-006',
                'total_item' => 3,
                'total_unit' => 2000,
                'penerima' => 'Brigadir Faisal Rahman',
                'kondisi' => 'Baik',
                'status' => 'Terverifikasi',
            ],
            [
                'id' => 7,
                'no_penerimaan' => 'REC-2024-007',
                'tanggal_terima' => '2024-12-07',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-007',
                'total_item' => 5,
                'total_unit' => 3000,
                'penerima' => 'Aipda Gunawan Wijaya',
                'kondisi' => 'Ada Cacat Minor',
                'status' => 'Dalam Review',
            ],
            [
                'id' => 8,
                'no_penerimaan' => 'REC-2024-008',
                'tanggal_terima' => '2024-12-08',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-008',
                'total_item' => 4,
                'total_unit' => 2800,
                'penerima' => 'Iptu Hendrik Susanto',
                'kondisi' => 'Baik',
                'status' => 'Dalam Review',
            ],
            [
                'id' => 9,
                'no_penerimaan' => 'REC-2024-009',
                'tanggal_terima' => '2024-12-09',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-009',
                'total_item' => 8,
                'total_unit' => 5500,
                'penerima' => 'Brigadir Irwan Setiawan',
                'kondisi' => 'Baik',
                'status' => 'Pending',
            ],
            [
                'id' => 10,
                'no_penerimaan' => 'REC-2024-010',
                'tanggal_terima' => '2024-12-09',
                'asal' => 'Gudang Pusat Korlantas Mabes Polri',
                'no_surat_jalan' => 'SJ-KORLANTAS-2024-010',
                'total_item' => 6,
                'total_unit' => 3800,
                'penerima' => 'Aiptu Joko Widodo',
                'kondisi' => 'Baik',
                'status' => 'Pending',
            ],
        ]);

        // Filter by search
        if ($this->search) {
            $receptions = $receptions->filter(function ($item) {
                return str_contains(strtolower($item['asal']), strtolower($this->search)) ||
                       str_contains(strtolower($item['no_penerimaan']), strtolower($this->search)) ||
                       str_contains(strtolower($item['no_surat_jalan']), strtolower($this->search)) ||
                       str_contains(strtolower($item['penerima']), strtolower($this->search));
            });
        }

        // Filter by status
        if ($this->filterStatus) {
            $receptions = $receptions->filter(function ($item) {
                return $item['status'] === $this->filterStatus;
            });
        }

        return $receptions->values();
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
        return view('livewire.admin.report.reception.admin-report-reception-index')
            ->layout('components.layouts.main.app');
    }
}
