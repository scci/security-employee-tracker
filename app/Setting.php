<?php

namespace SET;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class Setting.
 */
class Setting extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['key', 'value'];

    /**
     * Get a single setting value. If no value is set, return null.
     *
     * @param string      $key
     * @param string|null $default
     *
     * @return string
     */
    public static function get(string $key, string $default = null)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $dbValue = self::where('key', $key)->first();
        $value = isset($dbValue) ? json_decode($dbValue->value) : $default;
        Cache::forever($key, $value);

        return $value;
    }

    /**
     * Get all data in settings as an array.
     *
     * @return array
     */
    public static function getAll()
    {
        $array = [];
        $settings = self::all();
        foreach ($settings as $setting) {
            $array[$setting->key] = json_decode($setting->value);
        }

        return $array;
    }

    /**
     * Set a setting value. If no value is provided, it is stored as null.
     *
     * @param string      $key
     * @param mixed $value
     */
    public static function set(string $key, $value = null)
    {
        self::updateOrCreate(['key' => $key], ['value' => json_encode($value)]);
        Cache::forever($key, $value);
    }
}
