<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class QrCodeGenerator
{
    public static function generateForArtifact(string $qrCode): string
    {
        $qrCode = trim($qrCode);

        $renderer = new ImageRenderer(
            new RendererStyle(800, 8),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($qrCode);

        $filename = 'qrcodes/' . Str::slug($qrCode) . '-' . time() . '.svg';
        Storage::disk('public')->put($filename, $svg);

        return $filename;
    }
}
