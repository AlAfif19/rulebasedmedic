<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class ImageOptimizer
{
    public function storeMedicineImage(UploadedFile $file, string $directory): string
    {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'medicine';
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'svg') {
            $filename = $name.'-'.time().'.svg';
            $file->move($directory, $filename);

            return $filename;
        }

        $filename = $name.'-'.time().'.webp';
        $target = $directory.DIRECTORY_SEPARATOR.$filename;

        $this->compressRasterImage($file->getRealPath(), $target);

        return $filename;
    }

    private function compressRasterImage(string $sourcePath, string $targetPath): void
    {
        [$width, $height, $type] = getimagesize($sourcePath);

        $source = match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => imagecreatefromwebp($sourcePath),
            default => null,
        };

        if (!$source) {
            copy($sourcePath, $targetPath);
            return;
        }

        $maxDimension = 640;
        $scale = min(1, $maxDimension / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, imagecolorallocatealpha($canvas, 255, 255, 255, 127));
        imagecopyresized($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);
        imagewebp($canvas, $targetPath, 68);

        imagedestroy($source);
        imagedestroy($canvas);
    }
}
