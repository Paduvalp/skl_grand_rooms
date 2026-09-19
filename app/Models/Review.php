<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Guest reviews typed in by the admin.
 *
 * Deliberately never output as Review or AggregateRating structured data:
 * Google treats reviews a business publishes about itself as self-serving.
 */
class Review extends Model
{
    public const SOURCES = [
        'google' => 'Google',
        'walk-in' => 'Walk-in guest',
        'other' => 'Other',
    ];

    protected $fillable = [
        'guest_name',
        'rating',
        'review_text',
        'source',
        'stay_date',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'stay_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function scopeShown($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderByDesc('id');
    }

    public function sourceLabel(): string
    {
        return self::SOURCES[$this->source] ?? ucfirst((string) $this->source);
    }
}
