<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $guarded = [];
    public $timestamps = false;

    public function scopeProperty($query, $value)
    {
        return $query->where('name', $value);
    }
}
