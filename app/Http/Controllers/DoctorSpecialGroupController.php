<?php

namespace App\Http\Controllers;

use App\DoctorGroup;
use App\Exam;
use App\ExamGroup;
use App\ExamGroupExam;
use App\Group;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorSpecialGroupController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth:doctor');
    }


    public function group_list(){

        $data = [];

        $data[ 'groups' ] = DoctorGroup::join( 'group', 'group.id', '=', 'doctor_groups.group_id' )
            ->where( 'doctor_groups.doctor_id', Auth::guard('doctor')->id( ) )
            ->get( );


        return view( 'special-group.group-list', $data );

    }

    public function group_exams( $group_id ){

        $data = [];

        ExamGroup::where( 'group_id', $group_id )->select( 'id' );

        $data['exams']  = Exam::whereIn(
            'exam_group_exams.exam_group_id', ExamGroup::where( 'group_id', $group_id )->select( 'id' )
        )->join( 'exam_group_exams', 'exam_group_exams.exam_id', '=', 'exam.id' )
        ->paginate( 10 );

        return view('special-group.group-exams ', $data );

    }


}
