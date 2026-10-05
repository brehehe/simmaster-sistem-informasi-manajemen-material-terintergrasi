<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class DrawnSignatureService
{
    public function validate(string $value, string $field): string
    {
        $fail = fn () => throw ValidationException::withMessages([$field => 'Tanda tangan tidak valid. Gambar ulang atau unggah PNG/JPG.']);
        if (strlen($value) > 2800000 || ! str_starts_with($value, 'data:image/png;base64,')) {
            $fail();
        }
        $bytes = base64_decode(substr($value, 22), true);
        $size = $bytes ? @getimagesizefromstring($bytes) : false;
        if (! $size || $size[2] !== IMAGETYPE_PNG || $size[0] > 2000 || $size[1] > 1000) {
            $fail();
        }
        $image = @imagecreatefromstring($bytes);
        if (! $image) {
            $fail();
        }
        $ink = 0;
        for ($y = 0; $y < $size[1] && $ink < 8; $y++) {
            for ($x = 0; $x < $size[0] && $ink < 8; $x++) {
                $color = imagecolorsforindex($image, imagecolorat($image, $x, $y));
                if ($color['alpha'] < 100 && min($color['red'], $color['green'], $color['blue']) < 220) {
                    $ink++;
                }
            }
        }
        imagedestroy($image);
        if ($ink < 8) {
            $fail();
        }

        return 'data:image/png;base64,'.base64_encode($bytes);
    }
}
