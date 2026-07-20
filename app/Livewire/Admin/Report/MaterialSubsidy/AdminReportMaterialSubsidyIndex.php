<?php

namespace App\Livewire\Admin\Report\MaterialSubsidy;

use App\Models\Models\MenuPolda\MaterialSubsidy\MaterialSubsidyDetail;
use App\Models\Police\RegionalPolice;
use App\Models\Type\Type;
use App\Models\Type\TypeDetail;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReportMaterialSubsidyIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    #[Url]
    public $regionalPoliceId = '';

    #[Url]
    public $typeId = '';

    #[Url]
    public $typeDetailId = '';

    #[Url]
    public $filterStatus = '';

    public $startDate = '';
    public $endDate = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'regionalPoliceId' => ['except' => ''],
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

    public function getSubsidiesProperty()
    {
        $query = MaterialSubsidyDetail::query()
            ->with([
                'materialSubsidy.regionalPolice',
                'type',
                'typeDetail',
            ]);

        $query->join('material_subsidies', 'material_subsidy_details.material_subsidy_id', '=', 'material_subsidies.id')
            ->select('material_subsidy_details.*')
            ->where('material_subsidies.is_active', true);

        // Role filtering
        if (Auth::user()->hasRole('Polda')) {
            $query->where('material_subsidies.regional_police_id', Auth::user()->regional_police_id);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('material_subsidies.code', 'ilike', '%'.$this->search.'%')
                    ->orWhere('material_subsidies.recipient_name', 'ilike', '%'.$this->search.'%')
                    ->orWhere('material_subsidies.notes', 'ilike', '%'.$this->search.'%')
                    ->orWhereHas('materialSubsidy.regionalPolice', function ($polda) {
                        $polda->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhereHas('type', function ($t) {
                        $t->where('name', 'ilike', '%'.$this->search.'%');
                    })
                    ->orWhereHas('typeDetail', function ($td) {
                        $td->where('name', 'ilike', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->regionalPoliceId) {
            $query->where('material_subsidies.regional_police_id', $this->regionalPoliceId);
        }

        if ($this->typeId) {
            $query->where('material_subsidy_details.type_id', $this->typeId);
        }

        if ($this->typeDetailId) {
            $query->where('material_subsidy_details.type_detail_id', $this->typeDetailId);
        }

        if ($this->filterStatus) {
            $query->where('material_subsidies.status', $this->filterStatus);
        }

        if ($this->startDate) {
            $query->whereDate('material_subsidies.subsidy_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('material_subsidies.subsidy_date', '<=', $this->endDate);
        }

        return $query->orderBy('material_subsidies.subsidy_date', 'desc')->paginate($this->perPage);
    }

    public function updatedTypeId()
    {
        $this->typeDetailId = '';
        $this->resetPage();
    }

    public function updatedRegionalPoliceId()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        $regionalPolices = RegionalPolice::where('is_active', true)->orderBy('name')->get();

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

        return view('livewire.admin.report.material-subsidy.admin-report-material-subsidy-index', [
            'subsidyDetails' => $this->subsidies,
            'regionalPolices' => $regionalPolices,
            'allTypes' => $allTypes,
            'typeDetails' => $typeDetails,
        ])->layout('components.layouts.main.app');
    }
}
