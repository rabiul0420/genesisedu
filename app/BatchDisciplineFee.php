<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchDisciplineFee extends Model
{
    protected $table = 'batch_discipline_fees';
    public $timestamps = false;
    
    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id','id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }
    
}
