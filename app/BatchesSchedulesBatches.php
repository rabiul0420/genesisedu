<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchesSchedulesBatches extends Model
{
    //
    use SoftDeletes;
    protected $table = 'batches_schedules_batches';
    public $timestamps = false;




}
