<?php

namespace App\Services;

use App\Models\MachineImage;
use Illuminate\Support\Facades\Storage;

/**
 * Single source of truth for machine-image thumbnail generation and cleanup.
 *
 * Shared by the upload flow (MachineController), image removal
 * (MachineImageController) and the `machines:generate-thumbnails` backfill
 * command, so the GD logic lives in exactly one place.
 */
class ThumbnailService
{
    public const THUMB_MAX_WIDTH = 600;

    /**
     * Generate a resized thumbnail (max THUMB_MAX_WIDTH wide) using PHP's GD
     * extension. Returns the thumbnail path on the public disk, or null when:
     *  - the GD extension is not available;
     *  - the source file is missing or not a supported image;
     *  - the image is already narrower than the maximum width (use the original).
     *
     * When null is returned, callers keep thumb_path null so the thumb_url
     * accessor falls back to the original file — there are never broken images.
     */
    public function generate(string $originalPath): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $disk = Storage::disk('public');

        if ($originalPath === '' || ! $disk->exists($originalPath)) {
            return null;
        }

        $fullPath = $disk->path($originalPath);

        $info = @getimagesize($fullPath);
        if ($info === false) {
            return null;
        }

        [$width, $height] = $info;
        $type = $info[2] ?? null;

        if (! $width || ! $height || $width <= self::THUMB_MAX_WIDTH) {
            return null;
        }

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($fullPath),
            IMAGETYPE_PNG => @imagecreatefrompng($fullPath),
            IMAGETYPE_GIF => @imagecreatefromgif($fullPath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($fullPath) : false,
            default => false,
        };

        if (! $source) {
            return null;
        }

        $newWidth = self::THUMB_MAX_WIDTH;
        $newHeight = (int) round($height * ($newWidth / $width));

        $thumb = imagecreatetruecolor($newWidth, $newHeight);
        // White background flattens transparency (thumbnails are written as JPEG).
        $white = imagecolorallocate($thumb, 255, 255, 255);
        imagefilledrectangle($thumb, 0, 0, $newWidth, $newHeight, $white);
        imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($thumb, null, 80);
        $contents = ob_get_clean();

        imagedestroy($source);
        imagedestroy($thumb);

        if ($contents === false || $contents === '') {
            return null;
        }

        $thumbPath = 'machines/thumbs/' . pathinfo($originalPath, PATHINFO_FILENAME) . '.jpg';
        $disk->put($thumbPath, $contents);

        return $thumbPath;
    }

    /**
     * Delete an image's original file and its thumbnail (when present) from the
     * public disk. Missing files are ignored safely.
     */
    public function deleteImageFiles(MachineImage $image): void
    {
        $disk = Storage::disk('public');

        foreach ([$image->path, $image->thumb_path] as $path) {
            $path = (string) ($path ?? '');
            if ($path !== '' && $disk->exists($path)) {
                $disk->delete($path);
            }
        }
    }
}
