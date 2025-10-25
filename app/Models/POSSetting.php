<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POSSetting extends Model
{
    protected $table = 'pos_settings';
    
    protected $fillable = ['key', 'value', 'type'];
    
    public static function get($key, $default = null)
    {
        return \Cache::remember("pos_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }
    
    public static function set($key, $value, $type = 'text')
    {
        \Cache::forget("pos_setting_{$key}");
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}