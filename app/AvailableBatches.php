<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvailableBatches extends Model
{
    use SoftDeletes;

    protected $table = 'available_batches';

    protected $hidden = [
        'details'
    ];

    public function user( )
    {
        return $this->belongsTo('App\User','created_by','id');
    }


    public static function getCourseNames( ) {
        return [
            1 => "FCPS P-1",
            2 => "Residency",
            3 => "Outlier",
            4 => "Diploma",
            5 => "Combined",
        ];
    }

    public static function getCourselink( ) {
        return [[ "name" => "FCPS P-1","link" =>"batch-fcps-p-1"],
                [ "name" =>"Residency","link" =>"batch-residency"],
                [ "name" =>"Outlier","link" =>"batch-outlier"],
                [ "name" =>"Diploma","link" =>"batch-diploma"],
                [ "name" =>"Combined","link" =>"batch-combined"]
      
        ];
    }
    

    public function batch( )
    {
        return $this->belongsTo('App\Batches','batch_id','id');
    }

}
