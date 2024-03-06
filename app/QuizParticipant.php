<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizParticipant extends Model
{
    use SoftDeletes;

    public function doctor()
    {
        return $this->belongsTo(Doctors::class, 'doctor_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
