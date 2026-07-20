<?php

namespace App\Livewire\Admin\MenuPolres\MaterialUsageDetail;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Type\Type;
use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\MenuPolda\MaterialUsage\MaterialUsageDetail;
use Livewire\Attributes\Url;
use App\Models\Police\PoliceStation;
use App\Models\Type\TypeDetail;

class AdminMenuPolresMaterialUsageDetailIndex extends Component
{
    use WithPagination;

    #[Url]
    public $policeStationId = '';

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    #[Url]
    public $dateFrom = '';

    #[Url]
    public $dateTo = '';

    #[Url]
    public $usageType = '';

    public function mount()
    {
        // Default: tampilkan hari ini
        if (!$this->dateFrom && !$this->dateTo) {
            $this->dateFrom = now()->format('Y-m-d');
            $this->dateTo   = now()->format('Y-m-d');
        }
    }

    public function setDatePreset(string $preset)
    {
        match($preset) {
            'today'      => [$this->dateFrom, $this->dateTo] = [now()->format('Y-m-d'), now()->format('Y-m-d')],
            'yesterday'  => [$this->dateFrom, $this->dateTo] = [now()->subDay()->format('Y-m-d'), now()->subDay()->format('Y-m-d')],
            'this_week'  => [$this->dateFrom, $this->dateTo] = [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')],
            'this_month' => [$this->dateFrom, $this->dateTo] = [now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')],
            'all'        => [$this->dateFrom, $this->dateTo] = ['', ''],
            default      => null,
        };
        $this->resetPage();
    }

    public function updatedPoliceStationId() { $this->resetPage(); }
    public function updatedTypeId() { $this->resetPage(); }
    public function updatedTypeDetailId() { $this->resetPage(); }
    public function updatedDateFrom() { $this->resetPage(); }
    public function updatedDateTo() { $this->resetPage(); }
    public function updatedUsageType() { $this->resetPage(); }

    public function render()
    {
        $user = auth()->user();

        // Determine police station scope
        $scopedPoliceStationId = null;
        if ($user->hasRole('Admin') && $this->policeStationId) {
            $scopedPoliceStationId = $this->policeStationId;
        } elseif (!$user->hasRole('Admin')) {
            $scopedPoliceStationId = $user->police_station_id;
        }

        // Type query
        $typeQuery = Type::query();
        if ($user->userType && !empty($user->userType->types)) {
            $typeQuery->whereIn('id', $user->userType->types);
        }
        if ($this->typeId) {
            $typeQuery->where('id', $this->typeId);
        }
        $types = $typeQuery->get();

        $typeGroups = [];

        // Load filter options
        $policeStations = [];
        if ($user->hasRole('Admin')) {
            $policeStations = PoliceStation::orderBy('name')->get();
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
            $tdQuery = TypeDetail::query();
            if ($user->userType && !empty($user->userType->types)) {
                $tdQuery->whereIn('type_id', $user->userType->types);
            }
            $typeDetails = $tdQuery->orderBy('name')->get();
        }

        // Count totals for summary header
        $totalUsageCount = 0;
        $totalQty        = 0;

        foreach ($types as $type) {
            $detailQuery = MaterialUsageDetail::query()
                ->whereHas('materialUsage', function ($q) use ($scopedPoliceStationId) {
                    if ($scopedPoliceStationId) {
                        $q->where('police_station_id', $scopedPoliceStationId);
                    }
                    if ($this->dateFrom) {
                        $q->whereDate('date', '>=', $this->dateFrom);
                    }
                    if ($this->dateTo) {
                        $q->whereDate('date', '<=', $this->dateTo);
                    }
                })
                ->when($this->typeDetailId, fn($q) => $q->where('type_detail_id', $this->typeDetailId))
                ->when($this->usageType, fn($q) => $q->where('usage_type', $this->usageType))
                ->with([
                    'typeDetail',
                    'materialUsageDetailItems.service',
                    'materialUsageDetailItems.serviceDetail',
                    'materialUsage.policeStation',
                ])
                ->where('type_id', $type->id)
                ->orderBy('created_at', 'desc');

            $details = $detailQuery->paginate(10, ['*'], 'page_' . $type->id);

            if ($details->isEmpty()) continue;

            $services = \App\Models\Service\Service::with(['details'])
                ->where('type_id', $type->id)->get();

            $hasTypeDetails = $type->typeDetails()->exists();

            $groupTotalQty = $details->sum('quantity');
            $totalQty      += $groupTotalQty;
            $totalUsageCount += $details->total();

            $typeGroups[] = [
                'type'           => $type,
                'details'        => $details,
                'services'       => $services,
                'hasTypeDetails' => $hasTypeDetails,
                'groupTotalQty'  => $groupTotalQty,
            ];
        }

        return view('livewire.admin.menu-polres.material-usage-detail.admin-menu-polres-material-usage-detail-index', [
            'typeGroups'      => $typeGroups,
            'policeStations'  => $policeStations,
            'allTypes'        => $allTypes,
            'typeDetails'     => $typeDetails,
            'totalUsageCount' => $totalUsageCount,
            'totalQty'        => $totalQty,
        ])->layout('components.layouts.main.app');
    }
}
