<?php

use App\Services\DrawnSignatureService;
use Illuminate\Validation\ValidationException;

uses(Tests\TestCase::class);

function drawnSignatureFixture(bool $ink): string
{
    $image = imagecreatetruecolor(200, 60);
    imagefill($image, 0, 0, imagecolorallocate($image, 255, 255, 255));
    if ($ink) {
        imageline($image, 10, 20, 180, 40, imagecolorallocate($image, 20, 35, 60));
    }
    ob_start();
    imagepng($image);
    $bytes = ob_get_clean();
    imagedestroy($image);

    return 'data:image/png;base64,'.base64_encode($bytes);
}

it('accepts a drawn PNG signature', function () {
    $signature = drawnSignatureFixture(true);
    expect((new DrawnSignatureService)->validate($signature, 'signature'))->toBe($signature);
});

it('rejects an empty signature canvas', function () {
    (new DrawnSignatureService)->validate(drawnSignatureFixture(false), 'signature');
})->throws(ValidationException::class);

it('rejects malformed and non-image payloads', function ($value) {
    (new DrawnSignatureService)->validate($value, 'signature');
})->with(['data:image/png;base64,YWJj', 'data:image/svg+xml;base64,PHN2Zy8+', 'invalid'])->throws(ValidationException::class);
