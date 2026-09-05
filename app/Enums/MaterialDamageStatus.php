<?php

namespace App\Enums;

enum MaterialDamageStatus: string
{
    case REPORTED = 'reported';
    case UNDER_REVIEW = 'under_review';
    case APPROVED = 'approved';
    case DISPOSED = 'disposed';

    public function label(): string
    {
        return match ($this) {
            self::REPORTED => 'Dilaporkan',
            self::UNDER_REVIEW => 'Sedang Ditinjau',
            self::APPROVED => 'Disetujui',
            self::DISPOSED => 'Dimusnahkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::REPORTED => 'bg-amber-100 text-amber-700 border-amber-300',
            self::UNDER_REVIEW => 'bg-blue-100 text-blue-700 border-blue-300',
            self::APPROVED => 'bg-emerald-100 text-emerald-700 border-emerald-300',
            self::DISPOSED => 'bg-rose-100 text-rose-700 border-rose-300',
        };
    }
}
