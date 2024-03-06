<?php

namespace App\Http\Controllers;

use App\BatchesSchedules;
use App\DoctorClassRating;
use App\DoctorClassView;
use App\DoctorsCourses;
use App\ScheduleDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DoctorRattingController extends Controller
{


    function saveProgresses( Request $request ){

        $criteriaList = DoctorClassView::getAllCriteria();
        $allProgress = DoctorClassView::getAllProgresses();


        return $request->all();

        return DoctorClassRating::insert( $feedbacks );
    }

    function submit_doctor_ratting( Request $request ){

        $feedbacks = [];
        $feedbacksUpdates = [];
        $changed = false;


        ScheduleDetail::$_doctor_course_id = DoctorsCourses::where( 'batch_id', $request->batch_id )
            ->where( 'doctor_id', Auth::guard('doctor')->id() )->value( 'id' );

        $details = ScheduleDetail::with('doctor_class_view' )->find( $request->details_id );


        if( $details ) {

            $doctor_class_view = $details->doctor_class_view;

            if( $doctor_class_view instanceof DoctorClassView ) {

                if( $request->progress && is_array($request->progress) ) {
                    foreach ($request->progress as $variant => $progress )
                        $doctor_class_view->setRatings( $variant, $progress );
                }
                if( $request->feedback ) {
                    $doctor_class_view->feedback = $request->feedback;
                }

                $doctor_class_view->save( );

            }
        }

        return response( [ 'inserted' => $feedbacks, 'updated' => $feedbacksUpdates, 'changed' => $changed ] );
    }

    function doctor_ratting_modal( Request $request ){

        ScheduleDetail::$_doctor_course_id = DoctorsCourses::where( 'batch_id', $request->batch_id )
            ->where( 'doctor_id', Auth::guard('doctor')->id() )->value( 'id' );

        $details = ScheduleDetail::with('doctor_class_view', 'mentor', 'lectures.mentor' )
            ->find( $request->details_id );

        $feedback_criteria_list = [];

        $video_quality_criteria_list = DoctorClassView::getVideoQualityCriteriaList();

        if( $details ) {
            $feedback_criteria_list =  $details->type == 'Class'
                ? DoctorClassView::getClassCriteriaList() : DoctorClassView::getSolveClassCriteriaList( );

            if( $details->type == 'Exam' ) {
                $details = $details->lectures[0] ?? new ScheduleDetail();
            }
        }

        $feedbackData = [
            'doctor_id' => (int) Auth::guard('doctor')->id(),
            'details_id' => (int) $request->details_id,
        ];


        $doctor_class_view = $details->doctor_class_view ?? new DoctorClassView();
        $progresses = DoctorClassView::getProgresses( );
        $video_progresses = DoctorClassView::getVideoProgresses( );


        return view('batch_schedule.ratting-modal',
            compact('doctor_class_view','feedback_criteria_list','video_quality_criteria_list', 'details', 'progresses', 'video_progresses' ));
    }
}