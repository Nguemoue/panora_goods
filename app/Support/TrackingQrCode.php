<?php

namespace App\Support;

use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class TrackingQrCode
{
    public function dataUri(string $value): string
    {
        return (new QRCode(new QROptions([
            'outputBase64' => true,
            'outputType' => QROutputInterface::MARKUP_SVG,
            'svgUseFillAttributes' => true,
        ])))->render($value);
    }
}
