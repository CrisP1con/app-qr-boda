<?php

namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;

class QrCodeService
{
    public function destination(): string
    {
        return rtrim((string) config('app.url'), '/').'/boda';
    }

    public function result(string $format): ResultInterface
    {
        $qrCode = new QrCode(
            data: $this->destination(),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 500,
            margin: 20,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255),
        );

        return match ($format) {
            'png' => (new PngWriter)->write($qrCode),
            'svg' => (new SvgWriter)->write($qrCode),
            default => throw new \InvalidArgumentException('Formato QR no soportado.'),
        };
    }
}
