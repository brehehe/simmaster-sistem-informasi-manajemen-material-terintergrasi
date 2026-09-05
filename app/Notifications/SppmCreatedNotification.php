<?php

namespace App\Notifications;

use App\Models\MenuPolda\MaterialShipment\MaterialShipment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SppmCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected MaterialShipment $shipment
    ) {}

    /**
     * Saluran notifikasi yang digunakan.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data yang disimpan ke database notifications.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type'          => 'sppm_created',
            'shipment_id'   => $this->shipment->id,
            'shipment_code' => $this->shipment->code,
            'shipment_date' => $this->shipment->shipment_date,
            'total_items'   => $this->shipment->materialShipmentDetails()->count(),
            'sender_name'   => $this->shipment->senderRegionalPolice?->name ?? '-',
            'receiver_name' => $this->shipment->receiverPoliceStation?->name ?? '-',
            'message'       => 'SPPM baru telah diterbitkan: ' . $this->shipment->code
                . ' dari ' . ($this->shipment->senderRegionalPolice?->name ?? '-')
                . '. Silakan download QR Code untuk proses pengambilan di warehouse.',
            'url'           => '/menu-polres/material-shipment/receive/' . $this->shipment->id,
        ];
    }
}
