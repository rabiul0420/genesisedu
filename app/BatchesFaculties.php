<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchesFaculties extends Model
{
    //
    public $timestamps = null;
    protected $table = 'batches_faculties';
    use SoftDeletes;

}
