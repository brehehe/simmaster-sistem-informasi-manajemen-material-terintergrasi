<?php

namespace App\Livewire\Admin\Report\MaterialUsageDetail;

use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReportMaterialUsageDetailIndex extends Component
{
    use WithPagination;

    #[Url]
    public $policeStationId = '';

    #[Url]
    public $regionalPoliceId = '';

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    public function getTotalItemsProperty()
    {
        return $this->getFilteredQuery()->count();
    }

    public function getTotalQuantityProperty()
    {
        return $this->getFilteredQuery()->sum('quantity');
    }

    protected function getFilteredQuery()
    {
        $user = auth()->user();
        $query = MaterialUsageDetail::query()
            ->whereHas('materialUsage', function ($q) use ($user) {
                if ($user->hasRole('Admin')) {
                    if ($this->policeStationId) {
                        $q->where('police_station_id', $this->policeStationId);
                    }
                    if ($this->regionalPoliceId) {
                        $q->where('regional_police_id', $this->regionalPoliceId);
                    }
                } else {
                    if ($user->policeStation) {
                        $q->where('police_station_id', $user->policeStation->id);
                    }
                    if ($user->regionalPolice) {
                        $q->where('regional_police_id', $user->regionalPolice->id);
                    }
                }
            })
            ->when($this->typeId, function ($q) {
                $q->where('type_id', $this->typeId);
            })
            ->when($this->typeDetailId, function ($q) {
                $q->where('type_detail_id', $this->typeDetailId);
            });

        // Filter by user permissions for Types
        if ($user->userType && ! empty($user->userType->types)) {
            $query->whereIn('type_id', $user->userType->types);
        }

        return $query;
    }

    public function exportExcel()
    {
        $filters = [
            'policeStationId' => $this->policeStationId,
            'regionalPoliceId' => $this->regionalPoliceId,
            'typeId' => $this->typeId,
            'typeDetailId' => $this->typeDetailId,
        ];

        $fileName = 'Laporan_Detail_Penggunaan_Material_'.now()->format('YmdHis').'.xlsx';

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\MaterialUsageDetailExport($filters, auth()->user()),
            $fileName
        );
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

    public function exportPdf()
    {
        $filters = [
            'policeStationId' => $this->policeStationId,
            'regionalPoliceId' => $this->regionalPoliceId,
            'typeId' => $this->typeId,
            'typeDetailId' => $this->typeDetailId,
        ];

        $user = auth()->user();
        $query = Type::query();

        if ($user->userType && ! empty($user->userType->types)) {
            $query->whereIn('id', $user->userType->types);
        }
        if ($this->typeId) {
            $query->where('id', $this->typeId);
        }

        $types = $query->get();
        $reportData = [];

        foreach ($types as $type) {
            // Replicate the data fetching logic from render/getting properties
            $detailsQuery = MaterialUsageDetail::query()
                ->with(['typeDetail', 'materialUsageDetailItems', 'materialUsage.regionalPolice', 'materialUsage.policeStation']) // Eager load
                ->whereHas('materialUsage', function ($q) use ($user) {
                    if ($user->hasRole('Admin')) {
                        if ($this->policeStationId) {
                            $q->where('police_station_id', $this->policeStationId);
                        }
                        if ($this->regionalPoliceId) {
                            $q->where('regional_police_id', $this->regionalPoliceId);
                        }
                    } else {
                        if ($user->policeStation) {
                            $q->where('police_station_id', $user->policeStation->id);
                        }
                        if ($user->regionalPolice) {
                            $q->where('regional_police_id', $user->regionalPolice->id);
                        }
                    }
                })
                ->where('type_id', $type->id);

            if ($this->typeDetailId) {
                $detailsQuery->where('type_detail_id', $this->typeDetailId);
            }

            $details = $detailsQuery->get(); // Get all for PDF (no pagination)

            if ($details->isEmpty()) {
                continue;
            }

            $services = $type->services()->with(['details'])->orderBy('name')->get();
            $hasTypeDetails = $type->typeDetails()->exists();

            // --- Build Headers ---
            // Row 1
            $headerRow1 = [
                ['content' => 'No', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'center']],
            ];

            if ($hasTypeDetails) {
                $headerRow1[] = ['content' => 'Tipe Detail', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'left']];
            }
            if ($type->is_with_serial_number) {
                $headerRow1[] = ['content' => 'No Seri', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'left']];
            }

            // Services Row 1
            foreach ($services as $service) {
                $colSpan = $service->details->count() > 0 ? $service->details->count() : 1;
                $headerRow1[] = [
                    'content' => $service->name,
                    'colSpan' => $colSpan,
                    'styles' => ['valign' => 'middle', 'halign' => 'center'],
                ];
            }

            $headerRow1[] = ['content' => 'Jumlah', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'center']];
            $headerRow1[] = ['content' => 'Polda', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'left']];
            $headerRow1[] = ['content' => 'Polres', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'left']];

            // Row 2 (Service Details)
            $headerRow2 = [];
            foreach ($services as $service) {
                if ($service->details->count() > 0) {
                    foreach ($service->details as $sd) {
                        $headerRow2[] = ['content' => $sd->name, 'styles' => ['halign' => 'center']];
                    }
                } else {
                    // Empty for service without details, but autoTable handles rowSpan/colSpan
                    // Actually, if rowSpan is 2 for others, we don't put anything here?
                    // Wait, the Service header has colSpan. If colSpan=1 and rowSpan=1 (default), we need a cell below it?
                    // In the Blade view:
                    // If service->details->count() > 0: th colspan=count.
                    // Else: th rowspan=2.

                    // So we need to adjust headerRow1 for services without details.
                }
            }

            // Refine Header Row 1 logic for services without details
            $headerRow1_refined = [];
            // Base columns
            $headerRow1_refined[] = ['content' => 'No', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'center']];
            if ($hasTypeDetails) {
                $headerRow1_refined[] = ['content' => 'Tipe Detail', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'left']];
            }
            if ($type->is_with_serial_number) {
                $headerRow1_refined[] = ['content' => 'No Seri', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'left']];
            }

            foreach ($services as $service) {
                if ($service->details->count() > 0) {
                    $headerRow1_refined[] = [
                        'content' => $service->name,
                        'colSpan' => $service->details->count(),
                        'styles' => ['valign' => 'middle', 'halign' => 'center'],
                    ];
                } else {
                    $headerRow1_refined[] = [
                        'content' => $service->name,
                        'rowSpan' => 2,
                        'styles' => ['valign' => 'middle', 'halign' => 'center'],
                    ];
                }
            }
            $headerRow1_refined[] = ['content' => 'Jumlah', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'center']];
            $headerRow1_refined[] = ['content' => 'Polda', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'left']];
            $headerRow1_refined[] = ['content' => 'Polres', 'rowSpan' => 2, 'styles' => ['valign' => 'middle', 'halign' => 'left']];

            // --- Build Body ---
            $body = [];
            foreach ($details as $idx => $detail) {
                $row = [];
                $row[] = $idx + 1;

                if ($hasTypeDetails) {
                    $row[] = $detail->typeDetail->name ?? '-';
                }

                if ($type->is_with_serial_number) {
                    $parts = array_filter([$detail->item_code, $detail->number_serial_first, $detail->number_serial_second]);
                    $row[] = ! empty($parts) ? implode(' / ', $parts) : '0';
                }

                foreach ($services as $service) {
                    if ($service->details->count() > 0) {
                        foreach ($service->details as $sd) {
                            $item = $detail->materialUsageDetailItems
                                ->where('service_id', $service->id)
                                ->where('service_detail_id', $sd->id)
                                ->first();
                            $row[] = ($item && $item->quantity > 0) ? number_format($item->quantity, 0, ',', '.') : '0';
                        }
                    } else {
                        $item = $detail->materialUsageDetailItems
                            ->where('service_id', $service->id)
                            ->whereNull('service_detail_id')
                            ->first();
                        $row[] = ($item && $item->quantity > 0) ? number_format($item->quantity, 0, ',', '.') : '0';
                    }
                }

                $row[] = number_format($detail->quantity, 0, ',', '.');
                $row[] = $detail->materialUsage->regionalPolice->name ?? '-';
                $row[] = $detail->materialUsage->policeStation->name ?? '-';

                $body[] = $row;
            }

            // Headers passed to JS: [Row1, Row2]
            $headers = [$headerRow1_refined];
            if (! empty($headerRow2)) {
                $headers[] = $headerRow2;
            }

            $reportData[] = [
                'title' => $type->name,
                'headers' => $headers,
                'data' => $body,
            ];
        }

        // Deep sanitize reportData
        $jsonObj = json_decode(json_encode($reportData, JSON_INVALID_UTF8_SUBSTITUTE));
        $reportData = $this->sanitizeRecursive($jsonObj);

        $fileName = 'Laporan_Detail_Penggunaan_'.now()->format('YmdHis').'.pdf';

        $this->dispatch('export-usage-detail-pdf', [
            'reportData' => $reportData,
            'fileName' => $fileName,
        ]);
    }

    public function render()
    {
        $user = auth()->user();
        $query = Type::query();

        if ($user->userType && ! empty($user->userType->types)) {
            $query->whereIn('id', $user->userType->types);
        }

        // Filter Types query if specific type selected
        if ($this->typeId) {
            $query->where('id', $this->typeId);
        }

        $types = $query->get();
        $typeGroups = [];

        // Load filter options
        $policeStations = [];
        if ($user->hasRole('Admin')) {
            $policeStations = PoliceStation::orderBy('name')->get();
            $regionalPolices = RegionalPolice::orderBy('name')->get();
        }

        $allTypes = Type::query();
        if ($user->userType && ! empty($user->userType->types)) {
            $allTypes->whereIn('id', $user->userType->types);
        }
        $allTypes = $allTypes->orderBy('name')->get();

        $typeDetails = [];
        if ($this->typeId) {
            $typeDetails = TypeDetail::where('type_id', $this->typeId)->orderBy('name')->get();
        } else {
            // If no type selected, show all allowed type details? Or empty?
            // Usually better to show relevant ones or empty. Let's show all allowed if feasible or just depend on type.
            // For simplicity and performance, maybe fetch all allowed or let user select type first.
            // Let's fetch all allowed type details if no type selected, but limited by user permission
            $tdQuery = TypeDetail::query();
            if ($user->userType && ! empty($user->userType->types)) {
                $tdQuery->whereIn('type_id', $user->userType->types);
            }
            $typeDetails = $tdQuery->orderBy('name')->get();
        }

        foreach ($types as $type) {
            $details = MaterialUsageDetail::query()
                ->whereHas('materialUsage', function ($q) use ($user) {
                    if ($user->hasRole('Admin')) {
                        if ($this->policeStationId) {
                            $q->where('police_station_id', $this->policeStationId);
                        }
                        if ($this->regionalPoliceId) {
                            $q->where('regional_police_id', $this->regionalPoliceId);
                        }
                    } else {
                        if ($user->policeStation) {
                            $q->where('police_station_id', $user->policeStation->id);
                        }
                        if ($user->regionalPolice) {
                            $q->where('regional_police_id', $user->regionalPolice->id);
                        }
                    }
                })
                ->when($this->typeDetailId, function ($q) {
                    $q->where('type_detail_id', $this->typeDetailId);
                })
                ->with([
                    'typeDetail',
                    'materialUsageDetailItems.service',
                    'materialUsageDetailItems.serviceDetail',
                    'materialUsage.policeStation',
                    'materialUsage.regionalPolice',
                ])
                ->where('type_id', $type->id)
                ->paginate(5, ['*'], 'page_'.$type->id);

            // Fetch services for this specific type
            $services = \App\Models\Service\Service::with(['details'])
                ->where('type_id', $type->id)
                ->get();

            // Check if type has type details
            $hasTypeDetails = $type->typeDetails()->exists();

            // Only include types that have details
            if ($details->isNotEmpty()) {
                $typeGroups[] = [
                    'type' => $type,
                    'details' => $details,
                    'services' => $services,
                    'hasTypeDetails' => $hasTypeDetails,
                ];
            }
        }

        return view('livewire.admin.report.material-usage-detail.admin-report-material-usage-detail-index', [
            'typeGroups' => $typeGroups,
            'policeStations' => $policeStations,
            'regionalPolices' => $regionalPolices,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])->layout('components.layouts.main.app');
    }
}
