<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchesSchedulesSubjects extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected $table = 'batches_schedules_subjects';

    public function batch_schedule()
    {
        return $this->belongsTo('App\BatchesSchedules','schedule_id','id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

}
