<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case RECEIVED = 'received';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Terkirim',
            self::RECEIVED => 'Diterima',
            self::CANCELLED => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-gray-100 text-gray-700 border-gray-300',
            self::SENT => 'bg-blue-100 text-blue-700 border-blue-300',
            self::RECEIVED => 'bg-emerald-100 text-emerald-700 border-emerald-300',
            self::CANCELLED => 'bg-red-100 text-red-700 border-red-300',
        };
    }
}
