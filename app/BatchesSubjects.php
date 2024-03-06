<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchesSubjects extends Model
{
    //
    public $timestamps = null;
    protected $table = 'batches_subjects';
    use SoftDeletes;
}
