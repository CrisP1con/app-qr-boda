<?php

namespace App\Services;

use App\Models\Photo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class PhotoImageService
{
    /**
     * Store the original and the display derivatives for a photo.
     *
     * @return array{original_path: string, thumbnail_path: string, preview_path: string, mime_type: string, size: int}
     */
    public function storeOriginal(UploadedFile $file): array
    {
        $disk = Storage::disk('public');
        $baseName = (string) Str::uuid();
        $extension = strtolower($file->getClientOriginalExtension());
        $originalPath = "boda/originals/{$baseName}.{$extension}";
        $thumbnailPath = "boda/thumbnails/{$baseName}.jpg";
        $previewPath = "boda/previews/{$baseName}.jpg";

        $disk->putFileAs('boda/originals', $file, "{$baseName}.{$extension}");

        return [
            'original_path' => $originalPath,
            'thumbnail_path' => $thumbnailPath,
            'preview_path' => $previewPath,
            'mime_type' => (string) $file->getMimeType(),
            'size' => (int) $file->getSize(),
        ];
    }

    public function process(Photo $photo): void
    {
        $disk = Storage::disk('public');
        $source = @imagecreatefromstring($disk->get($photo->original_path));

        if ($source === false) {
            throw new RuntimeException('No se pudo procesar la imagen.');
        }

        try {
            $this->writeDerivative($source, $photo->thumbnail_path, 400);
            $this->writeDerivative($source, $photo->preview_path, 1600);
        } finally {
            imagedestroy($source);
        }
    }

    /**
     * @param  resource|\GdImage  $source
     */
    private function writeDerivative($source, string $path, int $maximumSize): void
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, $maximumSize / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        $background = imagecolorallocate($canvas, 255, 255, 255);

        imagefill($canvas, 0, 0, $background);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        ob_start();
        imagejpeg($canvas, null, 85);
        $contents = ob_get_clean();
        imagedestroy($canvas);

        if ($contents === false) {
            throw new RuntimeException('No se pudo generar una vista previa.');
        }

        Storage::disk('public')->put($path, $contents);
    }
}
