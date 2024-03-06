<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorAccess extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'exam_ids'      => 'array',
        'access_upto'   => 'datetime',
    ];

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }
}
