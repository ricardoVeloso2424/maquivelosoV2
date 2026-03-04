<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    public const SITE_SETTINGS_CACHE_KEY = 'settings.site';

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

    public static function getSiteSettings(array $defaults): array
    {
        return Cache::rememberForever(self::SITE_SETTINGS_CACHE_KEY, function () use ($defaults) {
            return static::getMany($defaults);
        });
    }

    public static function clearSiteSettingsCache(): void
    {
        Cache::forget(self::SITE_SETTINGS_CACHE_KEY);
    }

    public static function set(string $key, $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        static::clearSiteSettingsCache();
    }
}
