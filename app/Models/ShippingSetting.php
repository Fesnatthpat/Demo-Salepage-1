<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected static $cachedSettings = null;

    /**
     * Get a setting value by key.
     */
    public static function get($key, $default = null)
    {
        if (self::$cachedSettings === null) {
            self::$cachedSettings = self::pluck('value', 'key')->toArray();
        }

        return self::$cachedSettings[$key] ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set($key, $value)
    {
        $setting = self::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        self::$cachedSettings = null; // Clear cache
        return $setting;
    }
}
