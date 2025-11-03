<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get or set a setting value
     *
     * @param string|array|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key = null, $default = null)
    {
        if (is_null($key)) {
            return Setting::all()->pluck('value', 'key');
        }

        if (is_array($key)) {
            foreach ($key as $k => $v) {
                Setting::set($k, $v);
            }
            return true;
        }

        return Setting::get($key, $default);
    }
}

if (!function_exists('settings_by_category')) {
    /**
     * Get all settings grouped by category
     *
     * @return \Illuminate\Support\Collection
     */
    function settings_by_category()
    {
        return Setting::grouped();
    }
}

