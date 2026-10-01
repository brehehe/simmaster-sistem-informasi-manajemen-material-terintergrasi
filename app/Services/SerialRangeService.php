<?php

namespace App\Services;

use InvalidArgumentException;

class SerialRangeService
{
    public function parse(string $serial): array
    {
        $serial = trim($serial);
        if (! preg_match('/^(.*?)([0-9][0-9.]*)$/', $serial, $m)) {
            throw new InvalidArgumentException('Nomor seri harus diakhiri angka.');
        }
        $digits = str_replace('.', '', $m[2]);
        if (strlen($digits) > 15) {
            throw new InvalidArgumentException('Nomor seri terlalu panjang.');
        }

        return ['prefix' => $m[1], 'number' => (int) $digits, 'width' => strlen($digits), 'dotted' => str_contains($m[2], '.')];
    }

    public function format(int $number, string $template): string
    {
        $parts = $this->parse($template);
        $digits = str_pad((string) $number, $parts['width'], '0', STR_PAD_LEFT);
        if ($parts['dotted']) {
            $digits = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $digits);
        }

        return $parts['prefix'].$digits;
    }

    public function validate(string $first, string $last, int $quantity, ?string $batchFirst = null, ?string $batchLast = null): array
    {
        $a = $this->parse($first);
        $b = $this->parse($last);
        if ($a['prefix'] !== $b['prefix'] || $quantity < 1 || $quantity !== $b['number'] - $a['number'] + 1) {
            throw new InvalidArgumentException('Rentang nomor seri harus sesuai kuantitas (akhir − awal + 1).');
        }
        if ($batchFirst !== null && $batchLast !== null) {
            $start = $this->parse($batchFirst);
            $end = $this->parse($batchLast);
            if ($a['prefix'] !== $start['prefix'] || $b['prefix'] !== $end['prefix'] || $a['number'] < $start['number'] || $b['number'] > $end['number']) {
                throw new InvalidArgumentException('Nomor seri berada di luar sisa rentang batch.');
            }
        }

        return [$a['number'], $b['number']];
    }
}
