<?php

namespace App\Livewire\Admin\MenuPolda\Reception;

use App\Models\Reception\Reception;
use Livewire\Component;

class AdminMenuPoldaReceptionPrint extends Component
{
    public $receptionId;
    public $reception;

    public function mount($id)
    {
        $this->receptionId = $id;
        $this->reception = Reception::with([
            'regionalPolice', 
            'policeStation', 
            'typeMaterial',
            'receptionDetails.receptionDetailItems.type',
            'receptionDetails.receptionDetailItems.typeDetail',
            'receptionDetails.receptionDetailItems.service',
            'receptionDetails.receptionDetailItems.serviceDetail'
        ])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.admin.menu-polda.reception.admin-menu-polda-reception-print', [
            'receptionDetails' => $this->reception->getGroupedItems()
        ])->layout('components.layouts.print', ['title' => 'Berita Acara Penerimaan Materiil - ' . $this->reception->code]);
    }

    public function terbilang($angka) {
        return $this->reception->terbilang($angka);
    }
}
