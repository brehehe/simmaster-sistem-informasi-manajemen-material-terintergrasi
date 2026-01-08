<?php

namespace App\Livewire\Polres\MenuPolres\MaterialShipment;

use App\Models\Models\MenuPolda\MaterialShipment\MaterialShipment;
use Livewire\Component;
use Livewire\WithPagination;

class PolresMenuPolresMaterialShipmentReceiveIndex extends Component
{
    use WithPagination;

    public string $searchCode = '';
    public string $statusFilter = 'received'; // Default: only show shipped (pending receive)
    public int $perPage = 10;

    public function searchByCode()
    {
        if (empty($this->searchCode)) {
            session()->flash('error', 'Silakan masukkan kode pengiriman.');
            return;
        }

        $user = auth()->user();

        $shipment = MaterialShipment::where('code', $this->searchCode)
            ->where('receiver_police_station_id', $user->police_station_id)
            ->first();

        if (!$shipment) {
            session()->flash('error', 'Pengiriman dengan kode "' . $this->searchCode . '" tidak ditemukan atau bukan untuk Polres Anda.');
            return;
        }

        // Redirect to detail page
        return $this->redirect(route('menu-polres.material-shipment.receive.detail', ['id' => $shipment->id]), navigate: true);
    }

    public function render()
    {
        $user = auth()->user();

        $query = \App\Models\Models\MenuPolda\MaterialShipment\MaterialShipmentDetail::query()
            ->select('material_shipment_details.*')
            ->join('material_shipments', 'material_shipment_details.material_shipment_id', '=', 'material_shipments.id')
            ->join('types', 'material_shipment_details.type_id', '=', 'types.id')
            ->leftJoin('type_details', 'material_shipment_details.type_detail_id', '=', 'type_details.id')
            ->with(['materialShipment', 'materialShipment.senderRegionalPolice', 'materialShipment.receiverPoliceStation', 'type', 'typeDetail'])
            ->where('material_shipments.receiver_police_station_id', $user->police_station_id)
            ->where('material_shipments.is_active', true);

        if ($user->userType && !empty($user->userType->types)) {
            $query->whereIn('material_shipment_details.type_id', $user->userType->types);
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('material_shipments.status', $this->statusFilter);
        }

         if ($this->searchCode) {
             // If legacy searchCode use it, but user asked for generic search logic below
             // The user prompt asked to use specific search logic.
             // I will adapt the user's search logic here, using $this->searchCode as the input if user uses that input,
             // BUT normally "search" is the variable. The file has "public string $searchCode = '';"
             // I should probably check if I should rename $searchCode to $search?
             // The prompt is "searchnya buat seperti ini ... if ($this->search) ..."
             // I will assume I should use $this->searchCode as the variable but apply the logic to it.

            $keywords = preg_split('/\s+/', trim($this->searchCode));
            $query->where(function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {
                        $sub->where('material_shipments.code', 'ilike', "%{$word}%")
                            ->orWhere('types.name', 'ilike', "%{$word}%")
                            ->orWhere('type_details.name', 'ilike', "%{$word}%")
                            ->orWhere('material_shipment_details.code', 'ilike', "%{$word}%")
                            ->orWhere('material_shipment_details.number_serial_first', 'ilike', "%{$word}%")
                            ->orWhere('material_shipment_details.number_serial_second', 'ilike', "%{$word}%")
                            ->orWhere('material_shipment_details.description', 'ilike', "%{$word}%");
                    });
                }
            });
        }

        $materialShipments = $query->orderBy('material_shipments.shipment_date', 'desc')
             ->orderBy('material_shipments.created_at', 'desc')
             ->paginate($this->perPage);

        return view('livewire.polres.menu-polres.material-shipment.polres-menu-polres-material-shipment-receive-index', [
            'materialShipments' => $materialShipments,
        ])->layout('components.layouts.main.app');
    }
}
