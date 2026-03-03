<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, ?string $default = null): ?string
    {
        return static::query()
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public static function putValue(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getIntValue(string $key, int $default, int $min, int $max): int
    {
        $raw = static::getValue($key);

        if ($raw === null || filter_var($raw, FILTER_VALIDATE_INT) === false) {
            return $default;
        }

        $value = (int) $raw;

        if ($value < $min || $value > $max) {
            return $default;
        }

        return $value;
    }
}
