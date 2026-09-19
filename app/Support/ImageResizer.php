<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Saves an uploaded photo at a sensible size, plus a small copy for grids.
 *
 * Uses PHP's built-in GD extension, so there is no package to install. If
 * GD is missing on the server, or it cannot read the file, the original is
 * saved untouched and the site simply uses it for the small copy too.
 */
class ImageResizer
{
    /**
     * @param  string  $dir  Folder under public/, e.g. "uploads/gallery".
     * @return string  The new file name (no folder).
     */
    public static function store(UploadedFile $file, string $dir, int $maxWidth = 1600, int $thumbWidth = 600): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $ext = $ext === 'jpeg' ? 'jpg' : $ext;
        $name = Str::random(24).'.'.$ext;

        $fullDir = public_path($dir);
        $thumbDir = $fullDir.DIRECTORY_SEPARATOR.'thumbs';

        foreach ([$fullDir, $thumbDir] as $folder) {
            if (! is_dir($folder)) {
                mkdir($folder, 0755, true);
            }
        }

        $image = self::load($file->getRealPath(), $ext);

        if (! $image) {
            $file->move($fullDir, $name);

            return $name;
        }

        self::save(self::scaleDown($image, $maxWidth), $fullDir.DIRECTORY_SEPARATOR.$name, $ext);
        self::save(self::scaleDown($image, $thumbWidth), $thumbDir.DIRECTORY_SEPARATOR.$name, $ext);

        imagedestroy($image);

        return $name;
    }

    /** Removes a photo and its small copy. */
    public static function delete(?string $name, string $dir): void
    {
        if (! $name || str_contains($name, '/') || str_contains($name, '\\')) {
            return;
        }

        foreach ([public_path($dir.'/'.$name), public_path($dir.'/thumbs/'.$name)] as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    /** @return \GdImage|null */
    private static function load(string $path, string $ext)
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $image = match ($ext) {
            'jpg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        if (! $image) {
            return null;
        }

        // Phone photos are often stored sideways with a note saying which
        // way is up. Turn them the right way before resizing.
        if ($ext === 'jpg' && function_exists('exif_read_data')) {
            $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);
            $angle = match ($orientation) {
                3 => 180,
                6 => -90,
                8 => 90,
                default => 0,
            };

            if ($angle !== 0 && ($rotated = imagerotate($image, $angle, 0))) {
                imagedestroy($image);
                $image = $rotated;
            }
        }

        return $image;
    }

    /** A copy no wider than $width. Never scales up. */
    private static function scaleDown($image, int $width)
    {
        $w = imagesx($image);
        $h = imagesy($image);

        if ($w <= $width) {
            $width = $w;
        }

        $height = max(1, (int) round($h * $width / $w));
        $copy = imagecreatetruecolor($width, $height);

        // Keep transparent PNG / WEBP backgrounds transparent.
        imagealphablending($copy, false);
        imagesavealpha($copy, true);

        imagecopyresampled($copy, $image, 0, 0, 0, 0, $width, $height, $w, $h);

        return $copy;
    }

    private static function save($image, string $path, string $ext): void
    {
        match ($ext) {
            'png' => imagepng($image, $path, 7),
            'webp' => imagewebp($image, $path, 82),
            default => imagejpeg($image, $path, 82),
        };

        imagedestroy($image);
    }
}
