<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BatchFacultyFee extends Model
{
    protected $table = 'batch_faculty_fees';
    public $timestamps = false;
    
    public function batch()
    {
        return $this->belongsTo('App\Batches','batch_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculty','faculty_id','id');
    }
    
}
