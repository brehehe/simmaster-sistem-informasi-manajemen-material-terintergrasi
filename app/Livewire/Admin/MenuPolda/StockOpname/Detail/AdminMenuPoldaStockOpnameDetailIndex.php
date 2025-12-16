<?php

namespace App\Livewire\Admin\MenuPolda\StockOpname\Detail;

use App\Models\StockOpname\StockOpname;
use Livewire\Component;

class AdminMenuPoldaStockOpnameDetailIndex extends Component
{
    public StockOpname $opname;
    public $showApproveModal = false;

    public function mount($id)
    {
        $this->opname = StockOpname::with([
            'stockOpnameDetails.type',
            'stockOpnameDetails.typeDetail',
            'stockOpnameDetails.rack',
            'regionalPolice',
            'policeStation',
            'checkedByUser',
            'approvedByUser',
        ])->findOrFail($id);
    }

    public function markAsCompleted()
    {
        if ($this->opname->status !== 'draft') {
            session()->flash('error', 'Hanya stock opname dengan status draft yang bisa di-complete.');
            return;
        }

        $this->opname->markAsCompleted();
        $this->opname->refresh();

        session()->flash('success', 'Stock opname berhasil di-complete. Menunggu approval.');
    }

    public function openApproveModal()
    {
        if ($this->opname->status !== 'completed') {
            session()->flash('error', 'Hanya stock opname dengan status completed yang bisa di-approve.');
            return;
        }

        $this->showApproveModal = true;
    }

    public function closeModal()
    {
        $this->showApproveModal = false;
    }

    public function approve()
    {
        if ($this->opname->status !== 'completed') {
            session()->flash('error', 'Hanya stock opname dengan status completed yang bisa di-approve.');
            $this->closeModal();
            return;
        }

        try {
            $this->opname->approveAndAdjustStock(auth()->user());
            $this->opname->refresh();

            session()->flash('success', 'Stock opname berhasil di-approve dan stock telah disesuaikan.');
            $this->closeModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal approve stock opname: ' . $e->getMessage());
            $this->closeModal();
        }
    }

    public function render()
    {
        return view('livewire.admin.menu-polda.stock-opname.detail.admin-menu-polda-stock-opname-detail-index')
            ->layout('components.layouts.main.app');
    }
}
