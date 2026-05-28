<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public static function get(string $key, mixed $default = null): mixed
    {
        return cache()->remember("setting_{$key}", 3600, function () use ($key, $default) {
            return Setting::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        cache()->forget("setting_{$key}");
    }

    public static function all(): array
    {
        return Setting::pluck('value', 'key')->toArray();
    }
}
