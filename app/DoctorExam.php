<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorExam extends Model
{
    use SoftDeletes;
    public $timestamps = true;

    protected $table = 'doctor_exams';

    protected $guarded = [];

    public function doctor_course()
    {
        return $this->belongsTo('App\DoctorsCourses','doctor_course_id', 'id');
    }

    public function doctor_package()
    {
        return $this->belongsTo('App\DoctorPackage','doctor_package_id', 'id');
    }

    public function exam()
    {
        return $this->belongsTo('App\Exam','exam_id', 'id');
    }

    public function get_given_answers()
    {
        $file_path = !empty( $this->answer_file_link ) ? $this->answer_file_link : public_path( 'exam_answers/'.$this->doctor_course->doctor_id );

        $file_name = $this->exam_id . '_' . $this->doctor_course_id;

        if( !is_dir( $file_path ) ) {
            mkdir( $file_path, 0777, true );
        }

        $answers = null;

        //dd( $file );

        if( file_exists( $file = $file_path . '/' . $file_name . ".json" ) ) {
            $data = file_get_contents( $file );
            $answers = json_decode( $data, true );
        }
        
        return is_array( $answers ) ? $answers : [];
        
    }

}
