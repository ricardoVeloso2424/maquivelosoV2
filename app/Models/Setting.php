<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function getMany(array $defaults): array
    {
        if ($defaults === []) {
            return [];
        }

        $stored = static::query()
            ->whereIn('key', array_keys($defaults))
            ->pluck('value', 'key');

        foreach ($defaults as $key => $default) {
            $defaults[$key] = $stored[$key] ?? $default;
        }

        return $defaults;
    }

    public static function set(string $key, $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
