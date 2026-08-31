<?php

namespace App\Enums;

enum MutationStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case RECEIVED = 'received';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Menunggu',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::RECEIVED => 'Diterima',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::APPROVED => 'bg-blue-100 text-blue-800 border-blue-300',
            self::REJECTED => 'bg-red-100 text-red-800 border-red-300',
            self::RECEIVED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
        };
    }
}
