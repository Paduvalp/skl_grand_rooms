<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * A saved message. {name} in the text is replaced with the guest's name
 * from the phone book when the message goes out.
 */
class SmsTemplate extends Model
{
    /** What the front desk can drop into a message. */
    public const PLACEHOLDER = '{name}';

    /** Used when the number has no name saved against it. */
    public const FALLBACK_NAME = 'there';

    protected $fillable = ['title', 'body', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /** True when this message is written per guest. */
    public static function needsName(string $message): bool
    {
        return str_contains($message, self::PLACEHOLDER);
    }

    /**
     * "Hi {name}, ..." becomes "Hi Ravi, ...", or "Hi there, ..." when the
     * number has no name saved.
     */
    public static function withName(string $message, ?string $name): string
    {
        $name = trim((string) $name);

        // Just the first name reads better in a text, and saves characters.
        if ($name !== '') {
            $name = explode(' ', $name)[0];
        }

        return str_replace(self::PLACEHOLDER, $name !== '' ? $name : self::FALLBACK_NAME, $message);
    }

    /** How the message will read for a real guest, for the preview. */
    public function preview(): string
    {
        return self::withName($this->body, 'Ravi Kumar');
    }
}
