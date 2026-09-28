<?php

namespace App\Livewire\Admin\StockAdjustment;

use App\Models\Police\PoliceStation;
use App\Models\Police\RegionalPolice;
use App\Models\StockOpname\StockOpname;
use App\Services\StockAdjustmentService;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StockAdjustmentIndex extends Component
{
    #[Locked]
    public string $scope = 'polres';
    #[Locked]
    public string $token = '';
    #[Locked]
    public string $expected = '';
    public string $owner = '';
    public string $stockId = '';
    public string $quantity = '';
    public string $reason = '';

    public function mount(string $scope): void
    {
        abort_unless(in_array($scope, ['polda', 'polres'], true), 403);
        abort_unless(auth()->user()->hasRole(['Admin', ucfirst($scope)]), 403);
        $this->scope = $scope;
        $this->owner = (string) (auth()->user()->{$scope === 'polda' ? 'regional_police_id' : 'police_station_id'} ?? '');
        $this->token = (string) Str::uuid();
    }

    public function updatedOwner(): void
    {
        $this->reset('stockId', 'quantity', 'expected', 'reason');
        $this->resetValidation();
        $this->token = (string) Str::uuid();
    }

    public function updatedStockId(): void
    {
        $this->reset('quantity', 'expected');
        $this->resetValidation();
        $this->token = (string) Str::uuid();
        if ($this->stockId !== '') {
            $detail = app(StockAdjustmentService::class)->stocks(auth()->user(), $this->scope, $this->owner)->findOrFail($this->stockId);
            $this->expected = (string) $detail->quantity;
            $this->quantity = $this->expected;
        }
    }

    public function save(): void
    {
        $this->validate(['stockId' => 'required|uuid', 'quantity' => 'required|numeric|decimal:0,2', 'reason' => 'required|string|min:5|max:1000']);
        $record = app(StockAdjustmentService::class)->apply(auth()->user(), $this->scope, $this->owner,
            $this->stockId, $this->expected, $this->quantity, trim($this->reason), $this->token);
        session()->flash('success', 'Penyesuaian tersimpan. Kode: '.$record->code);
        $this->reset('stockId', 'quantity', 'expected', 'reason');
        $this->token = (string) Str::uuid();
    }

    public function render()
    {
        abort_unless(auth()->user()->hasRole(['Admin', ucfirst($this->scope)]), 403);
        $ownerModel = $this->scope === 'polda' ? RegionalPolice::class : PoliceStation::class;
        $owners = auth()->user()->hasRole('Admin') ? $ownerModel::where('is_active', true)->orderBy('name')->get() : collect();
        $stocks = collect();
        $history = collect();
        if ($this->owner !== '') {
            $stocks = app(StockAdjustmentService::class)->stocks(auth()->user(), $this->scope, $this->owner)
                ->with(['type', 'typeDetail', 'rack', 'service', 'serviceDetail'])->orderBy('type_id')->get();
            $history = StockOpname::where($this->scope === 'polda' ? 'regional_police_id' : 'police_station_id', $this->owner)
                ->whereNull($this->scope === 'polda' ? 'police_station_id' : 'regional_police_id')
                ->where('code', 'like', 'ADJ-%')->with(['stockOpnameDetails.type', 'checkedByUser'])->latest()->limit(20)->get();
        }
        return view('livewire.stock-adjustment.stock-adjustment-index', compact('owners', 'stocks', 'history'))
            ->layout('components.layouts.main.app', ['title' => 'Penyesuaian Stok']);
    }
}
