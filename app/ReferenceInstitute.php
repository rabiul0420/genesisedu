<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReferenceInstitute extends Model
{
    use SoftDeletes;
    protected $table = 'reference_institutes';
    public $timestamps = false;

}
