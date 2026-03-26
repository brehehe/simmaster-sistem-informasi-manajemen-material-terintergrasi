<?php

namespace App\Livewire\Admin\Report\MaterialDamage;

use App\Models\MenuPolda\MaterialDamage\MaterialDamageDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReportMaterialDamageIndex extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    #[Url]
    public $regionalPoliceId = '';

    #[Url]
    public $policeStationId = '';

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    #[Url]
    public $filterStatus = ''; // Renaming consistent with usage but keeping explicit name for clarity if needed

    public $startDate = '';

    public $endDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'regionalPoliceId' => ['except' => ''],
        'policeStationId' => ['except' => ''],
        'typeId' => ['except' => ''],
        'typeDetailId' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
    ];

    public function paginationView()
    {
        return 'vendor.livewire.custom-pagination';
    }

    public function getDamagesProperty()
    {
        $query = MaterialDamageDetail::query()
            ->with([
                'materialDamage.regionalPolice',
                'materialDamage.policeStation',
                'type',
                'typeDetail',
            ])
            ->where('material_damage_details.is_active', true);

        // Join to filter by material_damages table columns
        $query->join('material_damages', 'material_damage_details.material_damage_id', '=', 'material_damages.id')
            ->select('material_damage_details.*');

        // Role filtering
        if (Auth::user()->hasRole('Polda')) {
            $query->where('material_damages.regional_police_id', Auth::user()->regional_police_id)
                ->whereNull('material_damages.police_station_id');
        }

        if (Auth::user()->hasRole('Polres')) {
            $query->where('material_damages.police_station_id', Auth::user()->police_station_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('material_damages.code', 'ilike', '%'.$this->search.'%')
                    ->orWhere('material_damages.description', 'ilike', '%'.$this->search.'%')
                    ->orWhereHas('materialDamage.regionalPolice', function ($polda) {
                        $polda->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhereHas('materialDamage.policeStation', function ($polres) {
                        $polres->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhereHas('type', function ($t) {
                        $t->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhereHas('typeDetail', function ($td) {
                        $td->where('name', 'ilike', '%'.$this->search.'%');
                    });
            });
        }

        // Filters
        if ($this->regionalPoliceId) {
            $query->where('material_damages.regional_police_id', $this->regionalPoliceId);
        }

        if ($this->policeStationId) {
            $query->where('material_damages.police_station_id', $this->policeStationId);
        }

        if ($this->typeId) {
            $query->where('material_damage_details.type_id', $this->typeId);
        }

        if ($this->typeDetailId) {
            $query->where('material_damage_details.type_detail_id', $this->typeDetailId);
        }

        // Status filter
        if ($this->filterStatus) {
            $query->where('material_damages.status', $this->filterStatus);
        }

        // Date range
        if ($this->startDate) {
            $query->whereDate('material_damages.date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('material_damages.date', '<=', $this->endDate);
        }

        return $query->latest('material_damages.date')->paginate($this->perPage);
    }

    public function getRegionalPolicesProperty()
    {
        return RegionalPolice::where('is_active', true)->orderBy('name')->get();
    }

    public function getPoliceStationsProperty()
    {
        return PoliceStation::where('is_active', true)->orderBy('name')->get();
    }

    public function getAllTypesProperty()
    {
        return Type::orderBy('name')->get();
    }

    public function getStatusesProperty()
    {
        return [
            'reported' => 'Dilaporkan',
            'under_review' => 'Dalam Pemeriksaan',
            'approved' => 'Disetujui',
            'disposed' => 'Dimusnahkan',
        ];
    }

    // Summary statistics
    public function getTotalDamagesProperty()
    {
        // Total rows/items
        return $this->damages->total();
    }

    public function getTotalUnitsProperty()
    {
        // Replicating filter logic for sum
        $query = MaterialDamageDetail::query()
            ->join('material_damages', 'material_damage_details.material_damage_id', '=', 'material_damages.id')
            ->where('material_damage_details.is_active', true);

        if ($this->regionalPoliceId) {
            $query->where('material_damages.regional_police_id', $this->regionalPoliceId);
        }
        if ($this->policeStationId) {
            $query->where('material_damages.police_station_id', $this->policeStationId);
        }
        if ($this->typeId) {
            $query->where('material_damage_details.type_id', $this->typeId);
        }
        if ($this->typeDetailId) {
            $query->where('material_damage_details.type_detail_id', $this->typeDetailId);
        }
        if ($this->filterStatus) {
            $query->where('material_damages.status', $this->filterStatus);
        }
        if ($this->startDate) {
            $query->whereDate('material_damages.date', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $query->whereDate('material_damages.date', '<=', $this->endDate);
        }

        return $query->sum('material_damage_details.quantity');
    }

    public function getTodayDamagesProperty()
    {
        return MaterialDamageDetail::where('is_active', true)
            ->whereHas('materialDamage', function ($q) {
                $q->whereDate('date', today());
            })
            ->count();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedRegionalPoliceId()
    {
        $this->resetPage();
    }

    public function updatedPoliceStationId()
    {
        $this->resetPage();
    }

    public function updatedTypeId()
    {
        $this->resetPage();
        $this->typeDetailId = '';
    }

    public function updatedTypeDetailId()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        $filters = [
            'regionalPoliceId' => $this->regionalPoliceId,
            'policeStationId' => $this->policeStationId,
            'typeId' => $this->typeId,
            'typeDetailId' => $this->typeDetailId,
            'filterStatus' => $this->filterStatus,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $fileName = 'Laporan_Kerusakan_Material_'.now()->format('YmdHis').'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MaterialDamageExport($filters, auth()->user()),
            $fileName
        );
    }

    public function exportPdf()
    {
        $filters = [
            'regionalPoliceId' => $this->regionalPoliceId,
            'policeStationId' => $this->policeStationId,
            'typeId' => $this->typeId,
            'typeDetailId' => $this->typeDetailId,
            'filterStatus' => $this->filterStatus,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];

        $damages = $this->damages->items();

        // Deep sanitize
        $jsonObj = json_decode(json_encode($damages, JSON_INVALID_UTF8_SUBSTITUTE));
        $damages = $this->sanitizeRecursive($jsonObj);

        // Map data for JS
        $dataForPdf = [];
        foreach ($damages as $index => $detail) {
            $location = '-';
            if (! empty($detail->material_damage->regional_police->name)) {
                $location = $detail->material_damage->regional_police->name;
            } elseif (! empty($detail->material_damage->police_station->name)) {
                $location = $detail->material_damage->police_station->name;
            }

            $dateVal = $detail->material_damage->date ?? null;
            $damageDate = '-';
            if ($dateVal) {
                try {
                    $damageDate = \Carbon\Carbon::parse($dateVal)->format('d M Y');
                } catch (\Exception $e) {
                    $damageDate = $dateVal;
                }
            }

            $status = $detail->material_damage->status ?? '';
            $statusText = $this->statuses[$status] ?? $status;

            $dataForPdf[] = [
                $index + 1,
                $detail->material_damage->code ?? '-',
                $damageDate,
                $location,
                $detail->type->name ?? '-',
                $detail->type_detail->name ?? '-',
                number_format($detail->quantity ?? 0, 0, ',', '.'),
                $statusText,
                $detail->material_damage->description ?? '-',
            ];
        }

        $headers = ['No', 'Kode', 'Tanggal', 'Lokasi', 'Material', 'Detail Material', 'Qty', 'Status', 'Keterangan'];
        $fileName = 'Laporan_Kerusakan_Material_'.now()->format('YmdHis').'.pdf';

        $this->dispatch('export-damage-pdf', [
            'headers' => $headers,
            'data' => $dataForPdf,
            'fileName' => $fileName,
            'filters' => $filters,
        ]);
    }

    public function render()
    {
        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
            $typeDetails = TypeDetail::orderBy('name')->get();
        }

        return view('livewire.admin.report.material-damage.admin-report-material-damage-index', [
            'damages' => $this->damages,
            'typeDetails' => $typeDetails,
        ])
            ->layout('components.layouts.main.app');
    }

    private function sanitizeRecursive($data)
    {
        if (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        } elseif (is_array($data)) {
            $ret = [];
            foreach ($data as $i => $d) {
                $ret[$i] = $this->sanitizeRecursive($d);
            }

            return $ret;
        } elseif (is_object($data)) {
            if ($data instanceof \Illuminate\Database\Eloquent\Model) {
                $arr = $data->toArray();
                $cleanArr = $this->sanitizeRecursive($arr);

                return (object) $cleanArr;
            } elseif ($data instanceof \Illuminate\Support\Collection) {
                return $data->map(function ($item) {
                    return $this->sanitizeRecursive($item);
                });
            } else {
                $newData = new \stdClass;
                foreach ($data as $key => $value) {
                    $newData->$key = $this->sanitizeRecursive($value);
                }

                return $newData;
            }
        }

        return $data;
    }
}
