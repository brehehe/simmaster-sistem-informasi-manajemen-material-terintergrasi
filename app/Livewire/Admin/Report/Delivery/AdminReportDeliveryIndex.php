<?php

namespace App\Livewire\Admin\Report\Delivery;

use Livewire\Component;
use Livewire\WithPagination;

class AdminReportDeliveryIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $filterStatus = '';

    // Dummy data untuk pengiriman - ke kota-kota Jatim
    public function getDeliveriesProperty()
    {
        $deliveries = collect([
            [
                'id' => 1,
                'no_surat_jalan' => 'SJ-2024-001',
                'tanggal_kirim' => '2024-12-01',
                'tujuan' => 'Polres Surabaya',
                'alamat' => 'Jl. Sikatan No. 1, Surabaya',
                'total_item' => 5,
                'total_unit' => 1500,
                'kurir' => 'Bripka Andi',
                'kendaraan' => 'L 1234 POL',
                'status' => 'Terkirim',
                'tanggal_terima' => '2024-12-01',
            ],
            [
                'id' => 2,
                'no_surat_jalan' => 'SJ-2024-002',
                'tanggal_kirim' => '2024-12-02',
                'tujuan' => 'Polres Malang',
                'alamat' => 'Jl. Jaksa Agung Suprapto No. 19, Malang',
                'total_item' => 4,
                'total_unit' => 1200,
                'kurir' => 'Aipda Bambang',
                'kendaraan' => 'L 5678 POL',
                'status' => 'Terkirim',
                'tanggal_terima' => '2024-12-02',
            ],
            [
                'id' => 3,
                'no_surat_jalan' => 'SJ-2024-003',
                'tanggal_kirim' => '2024-12-03',
                'tujuan' => 'Polres Sidoarjo',
                'alamat' => 'Jl. Pahlawan No. 1, Sidoarjo',
                'total_item' => 4,
                'total_unit' => 1000,
                'kurir' => 'Brigadir Cahyo',
                'kendaraan' => 'L 9012 POL',
                'status' => 'Terkirim',
                'tanggal_terima' => '2024-12-03',
            ],
            [
                'id' => 4,
                'no_surat_jalan' => 'SJ-2024-004',
                'tanggal_kirim' => '2024-12-04',
                'tujuan' => 'Polres Gresik',
                'alamat' => 'Jl. Dr. Wahidin Sudirohusodo No. 245, Gresik',
                'total_item' => 6,
                'total_unit' => 1800,
                'kurir' => 'Aiptu Dodi',
                'kendaraan' => 'L 3456 POL',
                'status' => 'Terkirim',
                'tanggal_terima' => '2024-12-04',
            ],
            [
                'id' => 5,
                'no_surat_jalan' => 'SJ-2024-005',
                'tanggal_kirim' => '2024-12-05',
                'tujuan' => 'Polres Kediri',
                'alamat' => 'Jl. Basuki Rahmat No. 1, Kediri',
                'total_item' => 4,
                'total_unit' => 900,
                'kurir' => 'Bripka Eko',
                'kendaraan' => 'L 7890 POL',
                'status' => 'Terkirim',
                'tanggal_terima' => '2024-12-05',
            ],
            [
                'id' => 6,
                'no_surat_jalan' => 'SJ-2024-006',
                'tanggal_kirim' => '2024-12-06',
                'tujuan' => 'Polres Mojokerto',
                'alamat' => 'Jl. RA Basuni No. 7, Mojokerto',
                'total_item' => 5,
                'total_unit' => 1100,
                'kurir' => 'Aipda Faisal',
                'kendaraan' => 'L 2345 POL',
                'status' => 'Dalam Perjalanan',
                'tanggal_terima' => null,
            ],
            [
                'id' => 7,
                'no_surat_jalan' => 'SJ-2024-007',
                'tanggal_kirim' => '2024-12-07',
                'tujuan' => 'Polres Jember',
                'alamat' => 'Jl. Dr. Subandi No. 36, Jember',
                'total_item' => 3,
                'total_unit' => 750,
                'kurir' => 'Brigadir Gunawan',
                'kendaraan' => 'L 6789 POL',
                'status' => 'Dalam Perjalanan',
                'tanggal_terima' => null,
            ],
            [
                'id' => 8,
                'no_surat_jalan' => 'SJ-2024-008',
                'tanggal_kirim' => '2024-12-08',
                'tujuan' => 'Polres Banyuwangi',
                'alamat' => 'Jl. A. Yani No. 1, Banyuwangi',
                'total_item' => 7,
                'total_unit' => 2000,
                'kurir' => 'Aiptu Hendrik',
                'kendaraan' => 'L 0123 POL',
                'status' => 'Diproses',
                'tanggal_terima' => null,
            ],
            [
                'id' => 9,
                'no_surat_jalan' => 'SJ-2024-009',
                'tanggal_kirim' => '2024-12-09',
                'tujuan' => 'Polres Madiun',
                'alamat' => 'Jl. Pahlawan No. 45, Madiun',
                'total_item' => 4,
                'total_unit' => 850,
                'kurir' => 'Bripka Irwan',
                'kendaraan' => 'L 4567 POL',
                'status' => 'Diproses',
                'tanggal_terima' => null,
            ],
            [
                'id' => 10,
                'no_surat_jalan' => 'SJ-2024-010',
                'tanggal_kirim' => '2024-12-09',
                'tujuan' => 'Polres Blitar',
                'alamat' => 'Jl. Sudanco Supriyadi No. 25, Blitar',
                'total_item' => 2,
                'total_unit' => 500,
                'kurir' => 'Aipda Joko',
                'kendaraan' => 'L 8901 POL',
                'status' => 'Pending',
                'tanggal_terima' => null,
            ],
        ]);

        // Filter by search
        if ($this->search) {
            $deliveries = $deliveries->filter(function ($item) {
                return str_contains(strtolower($item['tujuan']), strtolower($this->search)) ||
                       str_contains(strtolower($item['no_surat_jalan']), strtolower($this->search)) ||
                       str_contains(strtolower($item['kurir']), strtolower($this->search));
            });
        }

        // Filter by status
        if ($this->filterStatus) {
            $deliveries = $deliveries->filter(function ($item) {
                return $item['status'] === $this->filterStatus;
            });
        }

        return $deliveries->values();
    }

    public function getStatusesProperty()
    {
        return [
            'Terkirim',
            'Dalam Perjalanan',
            'Diproses',
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
        return view('livewire.admin.report.delivery.admin-report-delivery-index')
            ->layout('components.layouts.main.app');
    }
}
