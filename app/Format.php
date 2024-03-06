<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Format extends Model
{
    use SoftDeletes;
    
    protected $guarded = [];

    protected $casts = [
        'histories' => 'array',
    ];

    protected $appends = [
        "keys",
    ];

    public static $raw_dynamic_value = false;

    const TYPES = [
        1 => "SMS",
    ];

    const PROPERTIES = [
        "BATCH_SHIFT_ON_PROCESS" => [
            '@DOCTOR_NAME@',
            '@FROM_BATCH@',
            '@TO_BATCH@'
        ],
    ];
    
    protected static $DYNAMIC_VALUES = [
        '@DOCTOR_NAME@' => '',
        '@DOCTOR_PHONE@' => '',
        '@FROM_BATCH@' => '',
        '@TO_BATCH@' => '',
        '@BATCH@' => '',
    ];

    public static function getMessage($property, $dynamic_values = [])
    {
        $keys = self::PROPERTIES[$property] ?? [];

        if(count($keys) !== count($dynamic_values)) {
            return "";
        }

        foreach($keys as $index => $key) {
            self::setDynamicValue($key, $dynamic_values[$index]);
        }

        $format = Format::query()
            ->type(1)
            ->property($property)
            ->first(['id', 'type', 'property', 'body']);

        return $format->body ?? "";
    }

    public static function setDynamicValue($keyword, $value)
    {
        if(isset(self::$DYNAMIC_VALUES[$keyword])) {
            self::$DYNAMIC_VALUES[$keyword] = $value;
            return true;
        }

        return false;
    }

    public static function getPropertyKeys()
    {
        return array_keys(self::PROPERTIES);
    }

    public static function getDynamicKeys()
    {
        return array_keys(self::$DYNAMIC_VALUES);
    }

    public static function getDynamicValues($key = null)
    {
        if($key != null) {
            return self::$DYNAMIC_VALUES[$key] ?? '';
        }

        return array_values(self::$DYNAMIC_VALUES);
    }

    public function getBodyAttribute($value)
    {
        return preg_replace_callback("/@[A-Z_]+@/", function( $match ){
            $match_string = $match[0] ?? '';

            return self::$raw_dynamic_value
                ? $match_string
                : (self::$DYNAMIC_VALUES[$match_string] ?? '');
        }, $value);
    }

    public function setBodyAttribute($value)
    {
        $this->attributes['body'] = preg_replace_callback("/@[A-Z_]+@/", function( $match ){
            $match_string = $match[0] ?? '';

            return isset(self::$DYNAMIC_VALUES[$match_string])
                ? $match_string
                : "";
        }, $value);
    }

    public function getKeysAttribute()
    {
        return self::PROPERTIES[$this->property] ?? [];
    }

    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeProperty($query, $property)
    {
        if(!in_array($property, self::getPropertyKeys())) {
            return $query->whereNull('property');
        }

        return $query->where('property', $property);
    }
}
