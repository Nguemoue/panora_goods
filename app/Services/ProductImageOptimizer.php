<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class ProductImageOptimizer
{
    private const int THUMB_MAX_DIMENSION = 480;

    private const int WEBP_QUALITY = 82;

    /**
     * Generate a lightweight WebP thumbnail on the public disk for a ProductImage.
     */
    public function generateThumbnail(ProductImage $image): ?string
    {
        if (! function_exists('imagewebp') || blank($image->path)) {
            return null;
        }

        $disk = Storage::disk('public');
        if (! $disk->exists($image->path)) {
            return null;
        }

        $sourcePath = $disk->path($image->path);
        $raw = @file_get_contents($sourcePath);
        if ($raw === false) {
            return null;
        }

        $src = @imagecreatefromstring($raw);
        if (! $src) {
            return null;
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        if ($origW <= 0 || $origH <= 0) {
            imagedestroy($src);

            return null;
        }

        $ratio = min(self::THUMB_MAX_DIMENSION / $origW, self::THUMB_MAX_DIMENSION / $origH, 1.0);
        $targetW = max(1, (int) round($origW * $ratio));
        $targetH = max(1, (int) round($origH * $ratio));

        $dst = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefill($dst, 0, 0, $transparent);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

        $baseName = pathinfo($image->path, PATHINFO_FILENAME);
        $thumbRelativePath = 'products/thumbs/'.$baseName.'-'.$image->id.'.webp';

        $disk->makeDirectory('products/thumbs');
        $saved = @imagewebp($dst, $disk->path($thumbRelativePath), self::WEBP_QUALITY);

        imagedestroy($src);
        imagedestroy($dst);

        if (! $saved) {
            return null;
        }

        if ($image->thumb_path !== $thumbRelativePath) {
            $image->updateQuietly(['thumb_path' => $thumbRelativePath]);
        }

        return $thumbRelativePath;
    }
}
