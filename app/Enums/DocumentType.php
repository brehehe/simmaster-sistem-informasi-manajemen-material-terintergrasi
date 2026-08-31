<?php

namespace App\Enums;

enum DocumentType: string
{
    case STNK = 'STNK';
    case TNKB = 'TNKB';
    case BPKB = 'BPKB';
    case SIM = 'SIM';

    public function label(): string
    {
        return $this->value;
    }
}
