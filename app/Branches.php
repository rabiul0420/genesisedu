<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branches extends Model
{
    use SoftDeletes;

    protected $table = "branches";
    public $timestamps = false; 
}
  