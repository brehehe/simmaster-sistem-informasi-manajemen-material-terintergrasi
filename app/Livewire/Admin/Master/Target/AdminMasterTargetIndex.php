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
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%')
                    ->orWhere('year', 'like', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.master.target.admin-master-target-index', ['targets' => $targets])
            ->layout('components.layouts.main.app');
    }
}
