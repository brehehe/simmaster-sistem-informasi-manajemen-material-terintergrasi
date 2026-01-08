<?php

namespace App\Livewire\Admin\Report\MaterialUsageDetail;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Type\Type;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use Livewire\Attributes\Url;
use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\Type\TypeDetail;

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

    public function render()
    {
        $user = auth()->user();
        $query = Type::query();

        if ($user->userType && !empty($user->userType->types)) {
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
        if ($user->userType && !empty($user->userType->types)) {
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
             if ($user->userType && !empty($user->userType->types)) {
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
                ->when($this->typeDetailId, function($q) {
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
                ->paginate(5, ['*'], 'page_' . $type->id);

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
                    'hasTypeDetails' => $hasTypeDetails
                ];
            }
        }

        return view('livewire.admin.report.material-usage-detail.admin-report-material-usage-detail-index', [
            'typeGroups' => $typeGroups,
            'policeStations' => $policeStations,
            'regionalPolices' => $regionalPolices,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails
        ])->layout('components.layouts.main.app');
    }
}
