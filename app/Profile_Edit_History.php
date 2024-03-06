<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Profile_Edit_History extends Model
{
    public $timestamps = false;
    protected $table = 'profile_edit_history';
    // public function discount(){
    //     return $this->belongsTo(Discount::class, 'discount_code_id');
    // }

    public function user(){
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function doctor(){
        return $this->belongsTo(Doctors::class, 'doctor_id','id');
    }
}
