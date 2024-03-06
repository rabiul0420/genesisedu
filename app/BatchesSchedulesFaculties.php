<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchesSchedulesFaculties extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected $table = 'batches_schedules_faculties';

    public function batch_schedule()
    {
        return $this->belongsTo('App\BatchesSchedules','schedule_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculties','faculty_id','id');
    }

}
