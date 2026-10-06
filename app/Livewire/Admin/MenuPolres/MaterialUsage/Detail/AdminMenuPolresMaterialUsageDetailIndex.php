<?php

namespace App\Livewire\Admin\MenuPolres\MaterialUsage\Detail;

use App\Models\MenuPolda\MaterialUsage\MaterialUsage;
use App\Models\Police\PoliceStation;
use App\Services\DailyMaterialUsageService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class AdminMenuPolresMaterialUsageDetailIndex extends Component
{
    #[Locked]
    public ?string $materialUsageId = null;

    public string $date = '';

    public string $policeStationId = '';

    public string $description = '';

    public array $quantities = [];

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole(['Admin', 'Polres']), 403);
    }

    public function mount($id = null): void
    {
        $this->materialUsageId = $id;
        $this->date = now('Asia/Jakarta')->toDateString();
        $this->policeStationId = auth()->user()->police_station_id ?? '';
        $usage = null;
        if ($id) {
            $usage = MaterialUsage::with('materialUsageDetails.materialUsageDetailItems')->findOrFail($id);
            $this->authorize('update', $usage);
            $this->date = $usage->date->toDateString();
            $this->policeStationId = $usage->police_station_id;
            $this->description = $usage->description ?? '';
        }
        $this->quantities = array_fill_keys(array_keys($this->catalog($usage)), 0);
        if ($usage) {
            foreach ($usage->materialUsageDetails as $detail) {
                foreach ($detail->materialUsageDetailItems as $item) {
                    $key = DailyMaterialUsageService::key($item->toArray());
                    if (array_key_exists($key, $this->quantities)) {
                        $this->quantities[$key] = (int) ($this->quantities[$key] ?: 0) + (int) $item->quantity;
                    }
                }
            }
        }
    }

    private function catalog(?MaterialUsage $editing = null): array
    {
        $user = auth()->user();

        if (! $editing && $this->materialUsageId) {
            $editing = MaterialUsage::with('materialUsageDetails.materialUsageDetailItems')->findOrFail($this->materialUsageId);
        }

        return app(DailyMaterialUsageService::class)->catalog(
            $user->hasRole('Admin') ? null : $user->userType?->types,
            false,
            $editing,
        );
    }

    public function save()
    {
        $this->validate(['policeStationId' => 'required|exists:police_stations,id', 'description' => 'nullable|string|max:1000']);
        app(DailyMaterialUsageService::class)->save(auth()->user(), $this->policeStationId, $this->date, $this->quantities, $this->description, $this->materialUsageId);
        session()->flash('success', 'Laporan penggunaan lengkap tersimpan. Stok dan penggunaan diperbarui.');

        return $this->redirect(route('menu-polres.material-usage'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.menu-polres.material-usage.detail.admin-menu-polres-material-usage-detail-index', [
            'rows' => $this->catalog(), 'policeStations' => auth()->user()->hasRole('Admin') ? PoliceStation::where('is_active', true)->orderBy('name')->get() : collect(),
        ])->layout('components.layouts.main.app');
    }
}
