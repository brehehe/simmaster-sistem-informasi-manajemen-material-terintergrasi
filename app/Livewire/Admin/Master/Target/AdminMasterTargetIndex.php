<?php

namespace App\Livewire\Admin\Master\Target;

use App\Models\Target\Target;
use Livewire\Component;
use Livewire\WithPagination;

class AdminMasterTargetIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public int $perPage = 5;

    protected $queryString = ['search' => ['except' => ''], 'perPage' => ['except' => 10]];

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function render(): \Illuminate\View\View
    {
        $targets = Target::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'ilike', '%'.$this->search.'%')
                    ->orWhere('description', 'ilike', '%'.$this->search.'%')
                    ->orWhere('year', 'ilike', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.target.admin-master-target-index', ['targets' => $targets])
            ->layout('components.layouts.main.app');
    }
}
