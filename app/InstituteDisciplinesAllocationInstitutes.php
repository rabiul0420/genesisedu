<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstituteDisciplinesAllocationInstitutes extends Model
{
    //
    use SoftDeletes;
    protected $table = 'institute_disciplines_allocation_institutes';
    public $timestamps = false;
    protected $guarded = [];
}
