<?php

namespace App\Actions;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class StoreOptimizedExportAttachment
{
    private const int MaxDimension = 2000;

    private const int WebpQuality = 85;

    public function handle(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $sourceContents = file_get_contents($file->getRealPath());
        $sourceImage = is_string($sourceContents) ? imagecreatefromstring($sourceContents) : false;

        if (! $sourceImage instanceof GdImage) {
            throw new RuntimeException('Lampiran gagal dibaca sebagai gambar.');
        }

        $optimizedImage = $this->resize($sourceImage);
        $encodedImage = $this->encodeAsWebp($optimizedImage);
        $path = trim($directory, '/').'/'.Str::ulid().'.webp';

        if (! Storage::disk($disk)->put($path, $encodedImage, ['visibility' => 'public'])) {
            throw new RuntimeException('Lampiran gagal disimpan.');
        }

        return $path;
    }

    private function resize(GdImage $sourceImage): GdImage
    {
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $scale = min(1, self::MaxDimension / max($sourceWidth, $sourceHeight));

        if ($scale === 1) {
            return $sourceImage;
        }

        $targetWidth = max(1, (int) round($sourceWidth * $scale));
        $targetHeight = max(1, (int) round($sourceHeight * $scale));
        $resizedImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);

        if (! imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight,
        )) {
            throw new RuntimeException('Lampiran gagal diperkecil.');
        }

        return $resizedImage;
    }

    private function encodeAsWebp(GdImage $image): string
    {
        ob_start();
        $isEncoded = imagewebp($image, null, self::WebpQuality);
        $encodedImage = ob_get_clean();

        if (! $isEncoded || ! is_string($encodedImage)) {
            throw new RuntimeException('Lampiran gagal dikompres.');
        }

        return $encodedImage;
    }
}
