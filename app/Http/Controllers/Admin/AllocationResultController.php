<?php

namespace App\Http\Controllers\Admin;

use App\Courses;
use App\DoctorInstituteChoice;
use App\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\InstituteAllocation;
use App\InstituteAllocationSeat;
use App\Result;
use App\Subjects;

class AllocationResultController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $course_id = $request->course_id;



        $exam = Exam::with('institute')
            ->when( $course_id , function($query) use ( $course_id ){
                $query->where('course_id', $course_id );
            })
            ->where('exam_type_id' , 13)->get();
        return view('admin.allocation-results.index', [
            'exams' => $exam->where('institute.id' , 6),
            'course_title'=>Courses::where( 'id', $course_id )->value('name'),
            'courses'=> Courses::where([ 'status' => 1, 'institute_id' => 6 ])->pluck( 'name', 'id' )
        ]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($exam_id)
    {
        $results = Result::with('doctor_course', 'doctor_course.subject')
            ->where('exam_id', $exam_id)->orderBy('obtained_mark', 'desc')->get();
        $disciplines = $results->unique('doctor_course.subject.name');
        
        return view('admin.allocation-results.show', [
            'disciplines' => $disciplines,
            'exam_id' => $exam_id
        ]);
    }

    public function list( $course_id, $exam_id, $discipline = 0 )
    {


        $remain_seat_private = $this->remain( $course_id, $exam_id, $discipline, 'private');
        $remain_seat_government = $this->remain( $course_id, $exam_id, $discipline, 'government');
        $remain_seat_bsmmu = $this->remain( $course_id, $exam_id, $discipline, 'bsmmu');
        $remain_seat_armed_forces = $this->remain( $course_id, $exam_id, $discipline, 'Armed Forces');
        $remain_seat_others = $this->remain( $course_id, $exam_id, $discipline, 'Others');

        //dd( $remain_seat_private , $remain_seat_government );
        $course = Courses::where(['id'=>$course_id])->first();
        $exam = Exam::where(['id'=>$exam_id])->first();

        return view('admin.allocation-results.list', [
            'allocated_private' => $this->allocated($remain_seat_private, $exam_id, $discipline, 'Autonomous/Private'),
            'allocated_government' => $this->allocated($remain_seat_government, $exam_id, $discipline, 'Government'),
            'allocated_bsmmu' => $this->allocated($remain_seat_bsmmu, $exam_id, $discipline, 'BSMMU'),
            'allocated_armed_forces' => $this->allocated($remain_seat_armed_forces, $exam_id, $discipline, 'Armed Forces'),
            'allocated_others' => $this->allocated($remain_seat_others, $exam_id, $discipline, 'Others'),
            'course'=> $course,
            'exam'=> $exam,
            'discipline'=>$discipline,
        ]);
    }

    public function print( $course_id, $exam_id, $discipline = 0 )
    {

        //dd( $remain_seat_private , $remain_seat_government );
        $course = Courses::where(['id'=>$course_id])->first();
        $exam = Exam::where(['id'=>$exam_id])->first();


        $remain_seat_private = $this->remain( $course_id, $exam_id, $discipline, 'private');
        $remain_seat_government = $this->remain( $course_id, $exam_id, $discipline, 'government');
        $remain_seat_bsmmu = $this->remain( $course_id, $exam_id, $discipline, 'bsmmu');
        $remain_seat_armed_forces = $this->remain( $course_id, $exam_id, $discipline, 'Armed Forces');
        $remain_seat_others = $this->remain( $course_id, $exam_id, $discipline, 'Others');

        //dd( $remain_seat_private , $remain_seat_government );
        $data['allocated_private'] = $this->allocated($remain_seat_private, $exam_id, $discipline, 'Autonomous/Private');
        $data['allocated_government'] = $this->allocated($remain_seat_government, $exam_id, $discipline, 'Government');
        $data['allocated_bsmmu'] = $this->allocated($remain_seat_bsmmu, $exam_id, $discipline, 'BSMMU');
        $data['allocated_armed_forces'] = $this->allocated($remain_seat_armed_forces, $exam_id, $discipline, 'Armed Forces');
        $data['allocated_others'] = $this->allocated($remain_seat_others, $exam_id, $discipline, 'Others');
        $data['course'] = $course;
        $data['exam'] = $exam;
        $data['discipline'] =$discipline;
        $discipline = Subjects::where(['course_id'=>$course->id,'name'=>$discipline])->first();
        
        if(isset($discipline) && isset($discipline->faculty->name))
        {
            $data['faculty'] = $discipline->faculty;
        }
        
        
        
        $allocated_institutes = array();
        foreach($data['allocated_private'] as $k=>$result)
        {
            if(isset($result->allocated_institute))
            {
                $allocated_institutes[$result->allocated_institute]['private'][$k]['obtained_mark'] = $result->obtained_mark;
                $allocated_institutes[$result->allocated_institute]['private'][$k]['registration_no'] = $result->doctor_course->reg_no;
                $allocated_institutes[$result->allocated_institute]['private'][$k]['allocated_institute'] = $result->allocated_institute;
            }            
        }
        foreach($data['allocated_government'] as $k=>$result)
        {
            if(isset($result->allocated_institute))
            {
                $allocated_institutes[$result->allocated_institute]['govt'][$k]['obtained_mark'] = $result->obtained_mark;
                $allocated_institutes[$result->allocated_institute]['govt'][$k]['registration_no'] = $result->doctor_course->reg_no;
                $allocated_institutes[$result->allocated_institute]['govt'][$k]['allocated_institute'] = $result->allocated_institute;
            }            
        }
        foreach($data['allocated_bsmmu'] as $k=>$result)
        {
            if(isset($result->allocated_institute))
            {
                $allocated_institutes[$result->allocated_institute]['bsmmu'][$k]['obtained_mark'] = $result->obtained_mark;
                $allocated_institutes[$result->allocated_institute]['bsmmu'][$k]['registration_no'] = $result->doctor_course->reg_no;
                $allocated_institutes[$result->allocated_institute]['bsmmu'][$k]['allocated_institute'] = $result->allocated_institute;
            }            
        }
        foreach($data['allocated_armed_forces'] as $k=>$result)
        {
            if(isset($result->allocated_institute))
            {
                $allocated_institutes[$result->allocated_institute]['armed_forces'][$k]['obtained_mark'] = $result->obtained_mark;
                $allocated_institutes[$result->allocated_institute]['armed_forces'][$k]['registration_no'] = $result->doctor_course->reg_no;
                $allocated_institutes[$result->allocated_institute]['armed_forces'][$k]['allocated_institute'] = $result->allocated_institute;
            }            
        }
        foreach($data['allocated_others'] as $k=>$result)
        {
            if(isset($result->allocated_institute))
            {
                $allocated_institutes[$result->allocated_institute]['others'][$k]['obtained_mark'] = $result->obtained_mark;
                $allocated_institutes[$result->allocated_institute]['others'][$k]['registration_no'] = $result->doctor_course->reg_no;
                $allocated_institutes[$result->allocated_institute]['others'][$k]['allocated_institute'] = $result->allocated_institute;
            }            
        }
        $data['allocated_institutes'] = $allocated_institutes;
        //echo "<pre>";print_r($data['allocated_institutes']);exit;       
        return view('admin.allocation-results.print', $data);
    }

    private function remain($course_id, $exam_id, $discipline, $type){
        $year = Exam::find($exam_id)->year;
        $remain_seat_private = InstituteAllocationSeat::with('instituteDiscipline')->where(['year' => $year, 'allocation_course_id' => $course_id ])->get();

        return $remain_seat_private
            ->where('instituteDiscipline.name', urldecode( $discipline ) )
            ->pluck($type,'institute_allocation_id')
            ->toArray();
    }

    private function allocated($remain, $exam_id, $discipline, $type){

        $results1 = Result::with('doctor_course')->where('exam_id', $exam_id)->orderBy('obtained_mark', 'desc')->get();
        $results2 = $results1->where('doctor_course.candidate_type', $type);
        $results = $results2->where('doctor_course.subject.name', $discipline);

        foreach($results as $k => $row){
            $choice = DoctorInstituteChoice::where(['exam_id' => $exam_id, 'doctor_course_id' => $row->doctor_course->id])->first();

            if($choice && $remain != []){
                $seat = isset($remain[$choice->first_institute]) ? $remain[$choice->first_institute] : 0;
                if($seat > 0){
                    $row->allocated_institute = InstituteAllocation::find($choice->first_institute)->name;
                    $remain[$choice->first_institute] -= 1;
                }else {
                    $seat = isset($remain[$choice->second_institute]) ? $remain[$choice->second_institute] : 0;
                    if($seat > 0){
                        $row->allocated_institute = InstituteAllocation::find($choice->second_institute)->name;
                        $remain[$choice->second_institute] -= 1;
                    }else {
                        $seat = isset($remain[$choice->third_institute]) ? $remain[$choice->third_institute] : 0;
                        if($seat > 0){
                            $row->allocated_institute = InstituteAllocation::find($choice->third_institute)->name;
                            $remain[$choice->third_institute] -= 1;
                        }
                    }
                }
            }
        }

        return $results;
    }

    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
