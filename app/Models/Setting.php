<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'category',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Boot the model and clear cache on changes
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            Cache::forget('settings.all');
        });

        static::deleted(function () {
            Cache::forget('settings.all');
        });
    }

    /**
     * Get a setting value by key
     */
    public static function get($key, $default = null)
    {
        $settings = Cache::remember('settings.all', 86400, function () {
            return static::all()->pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value = null, $type = 'string', $category = 'general')
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                static::set($k, $v);
            }
            return;
        }

        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->type = $type;
        $setting->category = $category;
        $setting->save();

        Cache::forget('settings.all');
    }

    /**
     * Get the value with proper type casting
     */
    public function getValueAttribute($value)
    {
        switch ($this->attributes['type'] ?? 'string') {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'decimal':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Set the value attribute
     */
    public function setValueAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['value'] = json_encode($value);
            $this->attributes['type'] = 'json';
        } elseif (is_bool($value)) {
            $this->attributes['value'] = $value ? '1' : '0';
            $this->attributes['type'] = 'boolean';
        } else {
            $this->attributes['value'] = $value;
        }
    }

    /**
     * Get all settings grouped by category
     */
    public static function grouped()
    {
        return static::all()->groupBy('category');
    }

    /**
     * Get settings by category
     */
    public static function byCategory($category)
    {
        return static::where('category', $category)->get();
    }
}
