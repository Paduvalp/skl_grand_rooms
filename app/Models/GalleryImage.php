<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryImage extends Model
{
    /** Folder under public/ where the photos live, next to the room photos. */
    public const DIR = 'uploads/gallery';

    public const CATEGORIES = [
        'exterior' => 'Exterior',
        'reception' => 'Reception',
        'rooms' => 'Rooms',
        'bathroom' => 'Bathroom',
        'parking' => 'Parking',
        'other' => 'Other',
    ];

    protected $fillable = [
        'image_path',
        'caption',
        'category',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** Active photos in the order the admin set. */
    public function scopeShown($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderByDesc('id');
    }

    public function url(): string
    {
        return asset(self::DIR.'/'.$this->image_path);
    }

    /** The small copy, or the full photo if no small copy could be made. */
    public function thumbUrl(): string
    {
        return file_exists(public_path(self::DIR.'/thumbs/'.$this->image_path))
            ? asset(self::DIR.'/thumbs/'.$this->image_path)
            : $this->url();
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? ucfirst((string) $this->category);
    }

    public function altText(): string
    {
        return $this->caption ?: 'SKL Grand Rooms – '.$this->categoryLabel();
    }

    /**
     * Width and height of a stored file, for the width/height attributes
     * that stop the page jumping while photos load.
     *
     * @return array{0: int, 1: int}
     */
    public function dimensions(bool $thumb = false): array
    {
        $path = public_path(self::DIR.'/'.($thumb ? 'thumbs/' : '').$this->image_path);

        if (! is_file($path)) {
            $path = public_path(self::DIR.'/'.$this->image_path);
        }

        $size = is_file($path) ? @getimagesize($path) : false;

        return $size ? [$size[0], $size[1]] : [800, 600];
    }
}
