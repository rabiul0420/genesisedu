<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    protected $table = 'module';

    use SoftDeletes, ScheduleDefs;

    public function module_type()
    {
        return $this->belongsTo('App\ScheduleModuleType','module_type_id','id');
    }

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id','id');
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id','id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }

    // public function batches()
    // {
    //     return $this->hasMany('App\ModuleContents','content_id','id')->where(['content_type_id'=>$this->module_content_types()[1]]);
    // }

    // public function programs()
    // {
    //     return $this->hasMany('App\ModuleContents','module_id','id')->where(['content_type_id'=>$this->module_content_types()[2]]);
    // }

    public function contents()
    {
        return $this->hasMany('App\ModuleContent','module_id','id');
    }

    public function batches()
    {
        $batch_ids = ModuleContent::where('module_id',$this->id)->where(['content_type_id'=>$this->get_module_content_type_id_from_name('Module Batch')])->pluck('content_id')->toArray();
        return Batches::whereIn('id',$batch_ids)->get();
    }

    public function topics()
    {
        $topic_ids = ModuleContent::where('module_id',$this->id)->where(['content_type_id'=>$this->get_module_content_type_id_from_name('Module Topic')])->pluck('content_id')->toArray();
        return Topic::whereIn('id',$topic_ids)->get();
    }

    public function program_types()
    {
        $program_type_ids = ModuleContent::where('module_id',$this->id)->where(['content_type_id'=>$this->get_module_content_type_id_from_name('Module Program Types')])->pluck('content_id')->toArray();
        return ScheduleProgramType::whereIn('id',$program_type_ids)->get();
    }

    public function media_types()
    {
        $module_media_type_ids = ModuleContent::where('module_id',$this->id)->where(['content_type_id'=>$this->get_module_content_type_id_from_name('Module Media Types')])->pluck('content_id')->toArray();
        return ScheduleMediaType::whereIn('id',$module_media_type_ids)->get();
    }

    public function programs()
    {
        $program_ids = ModuleContent::where('module_id',$this->id)->where(['content_type_id'=>$this->get_module_content_type_id_from_name('Module Program')])->pluck('content_id')->toArray();
        return Program::whereIn('id',$program_ids)->get();
    }

    public function faculties()
    {
        $faculty_ids = ModuleContent::where('module_id',$this->id)->where(['content_type_id'=>$this->get_module_content_type_id_from_name('Module Faculty')])->pluck('content_id')->toArray();
        return Faculty::whereIn('id',$faculty_ids)->get();
    }

    public function subjects()
    {
        $subject_ids = ModuleContent::where('module_id',$this->id)->where(['content_type_id'=>$this->get_module_content_type_id_from_name('Module Discipline')])->pluck('content_id')->toArray();
        return Subjects::whereIn('id',$subject_ids)->get();
    }

}
