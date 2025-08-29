<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class POSSetting extends Model
{
    protected $table = 'pos_settings';
    
    protected $fillable = ['key', 'value', 'type'];
    
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }
    
    public static function set($key, $value, $type = 'text')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }
}