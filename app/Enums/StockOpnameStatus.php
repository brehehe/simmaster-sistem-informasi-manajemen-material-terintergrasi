<?php

namespace App\Enums;

enum StockOpnameStatus: string
{
    case DRAFT = 'draft';
    case COMPLETED = 'completed';
    case APPROVED = 'approved';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::COMPLETED => 'Selesai',
            self::APPROVED => 'Disetujui',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-yellow-100 text-yellow-700 border-yellow-300',
            self::COMPLETED => 'bg-blue-100 text-blue-700 border-blue-300',
            self::APPROVED => 'bg-green-100 text-green-700 border-green-300',
        };
    }
}
