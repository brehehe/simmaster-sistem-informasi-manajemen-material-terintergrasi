<?php

namespace App\Enums;

enum SubsidyStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Diajukan',
            self::CONFIRMED => 'Dikonfirmasi',
            self::REJECTED => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-gray-100 text-gray-700 border-gray-300',
            self::SUBMITTED => 'bg-amber-100 text-amber-800 border-amber-300',
            self::CONFIRMED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::REJECTED => 'bg-rose-100 text-rose-800 border-rose-300',
        };
    }
}
