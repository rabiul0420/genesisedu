<?php

namespace App\Http\Controllers\Admin;

use App\BatchesSchedulesBatches;
use App\BatchesSchedulesLecturesExams;
use App\BatchesSchedulesSlots;
use App\BatchesSchedulesSlotTypes;
use App\BatchesSchedulesSubjects;
use App\BatchesSchedulesWeekDays;
use App\BatchesSchedulesFaculties;
use App\Exam_question;
use App\ExamAssign;
use App\ExamDiscipline;
use App\ExamFaculty;
use App\ExecutivesStuffs;
use App\Providers\AppServiceProvider;
use App\Subjects;
use App\Topics;
use App\WeekDays;
use App\Coming_by;
use App\ComingBy;
use App\Http\Controllers\Controller;

use App\MedicalColleges;
use App\RoomsTypes;
use Illuminate\Http\Request;

use App\Institutes;
use App\Courses;
use App\Teacher;
use App\Faculty;
use App\Subject;
use App\Batches;
use App\Doctors;
use App\Sessions;
use App\Service_packages;
use App\ServicePackages;
use App\BatchesSchedules;
use App\User;
use Session;
use Auth;
use Validator;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BatchesSchedulesController extends Controller
{
    //

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //Auth::loginUsingId(1);
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        /*  if(!$user->hasRole('Admin')){
              return abort(404);
          }*/

        $batches_schedules = BatchesSchedules::get();
        $title = 'SIF Admin : Batches Schedules List';
        $batches = Batches::get()->pluck('name', 'id');
        $courses= Courses::get()->pluck('name', 'id');
        $sessions= Sessions::get()->pluck('name', 'id');
        $years = array(''=>'Select year');
        for($year = date("Y");$year>=2017;$year--){
            $years[$year] = $year;
        }
        return view('admin.batches_schedules.list',['batches_schedules'=>$batches_schedules,'title'=>$title,'courses'=>$courses,'batches'=>$batches,'years'=>$years,'sessions'=>$sessions]);
    }

    public function batch_schedule_list(Request $request)
    {

        $year = $request->year;
        $course_id = $request->course_id;
        $session_id = $request->session_id;
        $batch_id = $request->batch_id;
        $batch_schedule_list = DB::table('batches_schedules as d1' )
                                ->leftjoin('courses as d2', 'd1.course_id', '=','d2.id')
                                ->leftjoin('service_packages as d3', 'd1.service_package_id', '=','d3.id')
                                ->leftjoin('batches as d4', 'd1.batch_id', '=','d4.id')
                                ->leftjoin('sessions as d5', 'd1.session_id', '=','d5.id')
                                ->whereNull('d1.deleted_at')
                                ;
        if($year){
            $batch_schedule_list = $batch_schedule_list->where('d1.year', '=', $year);
        }
        if($course_id){
            $batch_schedule_list = $batch_schedule_list->where('d1.course_id', '=', $course_id);
        }
        if($session_id){
            $batch_schedule_list = $batch_schedule_list->where('d1.session_id', '=', $session_id);
        }
        if($batch_id){
            $batch_schedule_list = $batch_schedule_list->where('d1.batch_id', '=', $batch_id);
        }

        $batch_schedule_list = $batch_schedule_list->select('d1.id as id','d1.name as batch_schedule','d1.year as year','d1.type as schedul_type','d2.name as course_name', 'd3.name as name','d4.name as batch_name','d5.name as session_name','d1.deleted_at');
        return Datatables::of($batch_schedule_list)
            ->addColumn('action', function ($batch_schedule_list) {
                return view('admin.batches_schedules.ajax_list',(['batch_schedule_list'=>$batch_schedule_list]));
            })

            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $user=BatchesSchedules::find(Auth::id());

        /*if(!$user->hasRole('Admin')){
            return abort(404);
        }*/
        $data['title'] = 'SIF Admin : Batches Schedules Create';
        $data['service_packages'] = ServicePackages::get()->pluck('name', 'id');
        $data['schedule_types'] = array(''=>'Select Type','R'=>'Regular','C'=>'Crush','M'=>'Mock','E'=>'Exam');
        $data['rooms_types'] = RoomsTypes::get()->pluck('room_name', 'id');
        $data['executive_list'] = ExecutivesStuffs::get()->where('emp_type','1')->pluck('name', 'id');
        $data['support_stuff_list'] = ExecutivesStuffs::get()->where('emp_type','2')->pluck('name', 'id');
        $data['papers'] = array(''=>'Select Paper','I'=>'Paper-I','II'=>'Paper-II','III'=>'Paper-III');
        $data['years'] = array(''=>'Select year');
        for($year = date("Y");$year>=2021;$year--){
            $data['years'][$year] = $year;
        }
        $data['sessions'] = Sessions::where('old','yes')->get()->pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');
        $data['week_days'] = WeekDays::pluck('name', 'id');
        $data['slots_list'] = BatchesSchedulesSlotTypes::get()->pluck('slot_name', 'slot_type');

        return view('admin.batches_schedules.create',$data);
    }


    public function updateBatchSchedulesBatches( $batch_ids, $schedule_id ) {
        if( BatchesSchedulesBatches::where( 'batch_schedule_id', $schedule_id )->exists() ) {
            BatchesSchedulesBatches::where( 'batch_schedule_id', $schedule_id  )->delete();
        }

        if( is_array( $batch_ids ) ) {
            $bundle_data = [];
            foreach ( $batch_ids as $batch_id ) {
                $bundle_data[] = [ 'batch_id' => $batch_id, 'batch_schedule_id' => $schedule_id ];
            }

            BatchesSchedulesBatches::insert( $bundle_data );
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'address' => ['required'],
            'room_id' => ['required'],
            'contact_details' => ['required'],
            'type' => ['required'],
            'service_package_id' => ['required'],
            'executive_id' => ['required'],
            'support_stuff_id' => ['required'],
            'year' => ['required'],
            'session_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'batch_id' => ['required'],
            'initial_date' => ['required'],
            'wd_ids' => ['required'],
            'slot_type'=> ['required'],
            'status'=> ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Enter Fields Correctly');
            return redirect()->action('Admin\BatchesSchedulesController@create')->withInput();
        }

        $batch_schedule = new BatchesSchedules();
        $batch_schedule->name = $request->name;
        $batch_schedule->tag_line = $request->tag_line;
        $batch_schedule->address = $request->address;
        $batch_schedule->room_id = $request->room_id;
        $batch_schedule->contact_details = $request->contact_details;
        $batch_schedule->type = $request->type;
        $batch_schedule->service_package_id = $request->service_package_id;
        $batch_schedule->executive_id = $request->executive_id;
        $batch_schedule->support_stuff_id = $request->support_stuff_id;
        $batch_schedule->paper = $request->paper;
        $batch_schedule->year = $request->year;
        $batch_schedule->session_id = $request->session_id;
        $batch_schedule->institute_id = $request->institute_id;
        $batch_schedule->course_id = $request->course_id;
        $batch_schedule->initial_date = $request->initial_date;
        $batch_schedule->status = $request->status;
        $batch_schedule->created_by = Auth::id();

        $batch_schedule->batch_id = $request->batch_id[0] ?? null;

        //dd( $request->batch_id );

        $batch_schedule->save();


        $schedule_id = $batch_schedule->id;

        $this->updateBatchSchedulesBatches( $request->batch_id, $schedule_id );

        if ($request->wd_ids) {

            if (BatchesSchedulesWeekDays::where('schedule_id', $schedule_id)->first()) {
                BatchesSchedulesWeekDays::where('schedule_id', $schedule_id)->delete();
            }

            $week_day_ids = $request->wd_ids;

            if (is_array($week_day_ids)) {
                foreach ($week_day_ids as $key => $value) {
                    unset($batch_schedule_week_days);
                    $batch_schedule_week_days = new BatchesSchedulesWeekDays();
                    $batch_schedule_week_days->schedule_id = $schedule_id;
                    $batch_schedule_week_days->day_id = $value;
                    $batch_schedule_week_days->status = $request->status;
                    $batch_schedule_week_days->created_by = Auth::id();
                    $batch_schedule_week_days->save();
                    $week_insert_id = $batch_schedule_week_days->id;
                    if ($week_insert_id) $week_insert_id_status = true;
                    else $week_insert_id_status = false;

                }
            }
        }


        if ($request->slot_type) {

            if (BatchesSchedulesSlots::where('schedule_id', $schedule_id)->first()) {
                BatchesSchedulesSlots::where('schedule_id', $schedule_id)->delete();
            }

            $slot_types = $request->slot_type;

            if (is_array($slot_types)) {
                foreach ($slot_types as $key => $value) {
                    if ($value && $_POST['start_time'][$key] && $_POST['end_time'][$key]) {
                        unset($batch_schedule_slots);
                        $batch_schedule_slots = new BatchesSchedulesSlots();
                        $batch_schedule_slots->schedule_id = $schedule_id;
                        $batch_schedule_slots->slot_type = $value;
                        $batch_schedule_slots->start_time = $_POST['start_time'][$key];
                        $batch_schedule_slots->end_time = $_POST['end_time'][$key];
                        $batch_schedule_slots->status = $request->status;
                        $batch_schedule_slots->created_by = Auth::id();
                        $batch_schedule_slots->save();
                        $slot_type_insert_id = $batch_schedule_slots->id;
                        if ($slot_type_insert_id) $slot_type_insert_id_status = true;
                        else $slot_type_insert_id_status = false;
                    }
                }
            }
        }

        $this->save_subject_faculty_relation( $schedule_id, $request );

        Session::flash('message', 'Record has been added successfully');
        return redirect()->action('Admin\BatchesSchedulesController@lectures_exams_save',  [$schedule_id] );

    }

    function save_subject_faculty_relation( $schedule_id, Request $request ){
        $institute = Institutes::find( $request->institute_id );

        if( $institute->type == 1 ) {

            $this->save_relation(
                BatchesSchedulesFaculties::class,
                [ 'schedule_id' => $schedule_id ],
                $request->faculty_id,
                ['schedule_id' => $schedule_id, 'faculty_id' => '@value@']
            );
        }

        if( $institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {
            $this->save_relation(
                BatchesSchedulesSubjects::class,
                [ 'schedule_id' => $schedule_id ],
                $request->subject_id,
                ['schedule_id' => $schedule_id, 'subject_id' => '@value@']
            );
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=BatchesSchedules::select('batches_schedules.*')
            ->find($id);
        return view('admin.batches_schedules.show',['user'=>$user]);
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {


        $data['title'] = 'SIF Admin : Batches Schedules Edit';
        $data['service_packages'] = ServicePackages::get()->pluck('name', 'id');
        $data['schedule_types'] = array(''=>'Select Type','R'=>'Regular','C'=>'Crush','M'=>'Mock','E'=>'Exam');
        $data['rooms_types'] = RoomsTypes::get()->pluck('room_name', 'id');
        $data['executive_list'] = ExecutivesStuffs::get()->where('emp_type','1')->pluck('name', 'id');
        $data['support_stuff_list'] = ExecutivesStuffs::get()->where('emp_type','2')->pluck('name', 'id');
        $data['papers'] = array(''=>'Select Paper','I'=>'Paper-I','II'=>'Paper-II','III'=>'Paper-III');
        $data['years'] = array(''=>'Select year');
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }
        $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');
        $data['week_days'] = WeekDays::pluck('name', 'id');
        $data['slots_list'] = BatchesSchedulesSlotTypes::get()->pluck('slot_name', 'slot_type');

        $schedule_details = BatchesSchedules::find($id);

        $data['schedule_details'] = $schedule_details;

        $data['topics_list'] = Topics::get()->where('course_id', $schedule_details->course_id)->sortBy('name')->pluck('name','id');

        //$data['wd_ids'] = WeekDays::get()->whereIn('wd_id',BatchesSchedulesWeekDays::get()->where('schedule_id',$id)->pluck('day_id'))->pluck('wd_id');
        //$data['batch_slots'] = BatchesSchedulesSlotTypes::get()->whereIn('slot_type',BatchesSchedulesSlots::get()->where('schedule_id',$id)->pluck('slot_type'))->pluck('slot_type');
        $data['wd_ids'] = BatchesSchedulesWeekDays::get()->where('schedule_id',$id)->pluck('day_id')->toArray();
        $data['batch_slots'] = BatchesSchedulesSlots::where('schedule_id',$id)->get();
        $data['teachers_list'] = Teacher::get()->pluck('name','id');

        $data['courses'] = Courses::get()->where('institute_id',$data['schedule_details']->institute_id)->pluck('name', 'id');
        $data['batches'] = Batches::get()->where('institute_id',$data['schedule_details']->institute_id)
            ->where('course_id',$data['schedule_details']->course_id)
            ->pluck('name', 'id');


        $course = Courses::find( $schedule_details->course_id );
        $data['selected_batches'] = BatchesSchedulesBatches::where( 'batch_schedule_id', $id )->pluck( 'batch_id' )->toArray();

        $is_combined = $schedule_details->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID;

        if($data['schedule_details']->institute->type==1){
            $data['faculties'] = Faculty::where('course_id',$data['schedule_details']->course_id)->pluck('name', 'id');

            if( $is_combined ) {
                $data[ 'faculties' ] = $course->combined_faculties()->pluck('name', 'id');
            }

            $data['subjects'] = Subjects::where('faculty_id',$data['schedule_details']->faculty_id)->pluck('name', 'id');

            $batches_shcedules_faculties = BatchesSchedulesFaculties::where('schedule_id',$id)->get();
            $selected_faculties = array();
            foreach($batches_shcedules_faculties as $faculty)
            {
                $selected_faculties[] = $faculty->faculty_id;
            }

            $data['selected_faculties'] = collect($selected_faculties);

        }

        if( $data['schedule_details']->institute->type == 0 || $schedule_details->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ){
            $data['subjects'] = Subjects::where( 'course_id', $data['schedule_details']->course_id )->pluck('name', 'id');

            if( $is_combined ) {
                $data[ 'subjects' ] = $course->combined_disciplines( )->pluck('name', 'id');
            }

            $batches_shcedules_subjects = BatchesSchedulesSubjects::where('schedule_id',$id)->get();
            $selected_subjects = array();
            foreach($batches_shcedules_subjects as $subject)
            {
                $selected_subjects[] = $subject->subject_id;
            }

            $data['selected_subjects'] = collect($selected_subjects);
        }



        //echo "<pre>";print_r($selected_faculties);exit;
        $initial_date = $data['schedule_details']->initial_date;
        $weekdays = array('1'=>'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $array_weekday = $data['wd_ids'];



        // Create a new DateTime object
        $date = new \DateTime($initial_date);
        $dates = array();
        $j = array_search($value = $date->format('N'), $array_weekday);

        for($i = 1;$i<=count($data['topics_list']);$i++){
            // Create a new DateTime object
            $date = new \DateTime($date->format('Y-m-d'));

            if(count($array_weekday)>1){

                // Modify the date it contains
                if($i==1){
                    $dates[] = $date->modify($weekdays[$array_weekday[$j++]])->format('Y-m-d');
                    if($j > count($array_weekday)-1 )$j = 0;
                }
                else {
                    if($j == 0 || $j == count($array_weekday)-1){$dates[] = $date->modify($weekdays[$array_weekday[$j++]])->format('Y-m-d');}
                    else $dates[] = $date->modify('next '.$weekdays[$array_weekday[$j++]])->format('Y-m-d');
                    if($j > count($array_weekday)-1 )$j = 0;
                }

            }
            else if(count($array_weekday)==1){

                if($i==1){
                    $dates[] = $date->modify($weekdays[$array_weekday[$j]])->format('Y-m-d');
                }
                else {

                    $dates[] = $date->modify('next ' . $weekdays[$array_weekday[$j]])->format('Y-m-d');
                }
            }
        }

        $data['schedule_dates'] = $dates;
        $data['schedule_dates'] =  BatchesSchedulesLecturesExams::where('schedule_id',$id)->distinct('schedule_date')->pluck('schedule_date')->toArray();
        $schedules =  BatchesSchedulesLecturesExams::where('schedule_id',$id)->get();

        $custom = array();
        $i = 1;
        foreach ($schedules as $keys=>$values)
        {
            $custom[$values['schedule_date']][] = $values;
            $i++;
        }
        $data['schedule_rows'] = $custom;

        if(count($data['schedule_rows']) > 0){
            return view('admin.batches_schedules.edit', $data);
        }
        else {

            $initial_date = $data['schedule_details']->initial_date;
            $weekdays = array('1'=>'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
            $array_weekday = $data['wd_ids'];



            // Create a new DateTime object
            $date = new \DateTime($initial_date);
            $dates = array();
            $j = array_search($value = $date->format('N'), $array_weekday);

            for($i = 1;$i<=count($data['topics_list']);$i++){
                // Create a new DateTime object
                $date = new \DateTime($date->format('Y-m-d'));

                if(count($array_weekday)>1){

                    // Modify the date it contains
                    if($i==1){
                        $dates[] = $date->modify($weekdays[$array_weekday[$j++]])->format('Y-m-d');
                        if($j > count($array_weekday)-1 )$j = 0;
                    }
                    else {
                        if($j == 0 || $j == count($array_weekday)-1){$dates[] = $date->modify($weekdays[$array_weekday[$j++]])->format('Y-m-d');}
                        else $dates[] = $date->modify('next '.$weekdays[$array_weekday[$j++]])->format('Y-m-d');
                        if($j > count($array_weekday)-1 )$j = 0;
                    }

                }
                else if(count($array_weekday)==1){

                    if($i==1){
                        $dates[] = $date->modify($weekdays[$array_weekday[$j]])->format('Y-m-d');
                    }
                    else {

                        $dates[] = $date->modify('next ' . $weekdays[$array_weekday[$j]])->format('Y-m-d');
                    }

                }

            }

            $data['schedule_dates'] = $dates;
            return view('admin.batches_schedules.schedules_lectures_exams_save', $data);
        }



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
        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'address' => ['required'],
            'room_id' => ['required'],
            'contact_details' => ['required'],
            'type' => ['required'],
            'service_package_id' => ['required'],
            'executive_id' => ['required'],
            'support_stuff_id' => ['required'],
            'year' => ['required'],
            'session_id' => ['required'],
            'institute_id' => ['required'],
            'course_id' => ['required'],
            'batch_id' => ['required'],
            'initial_date' => ['required'],
            'wd_ids' => ['required'],
            'slot_type'=> ['required'],
            'status'=> ['required'],
        ]);

        if ($validator->fails()){
            Session::flash('class', 'alert-danger');
            return redirect()->action('Admin\BatchesSchedulesController@edit')->withInput();
        }

        $batch_schedule = BatchesSchedules::find($id);

        $batch_schedule->name = $request->name;
        $batch_schedule->tag_line = $request->tag_line;
        $batch_schedule->address = $request->address;
        $batch_schedule->room_id = $request->room_id;
        $batch_schedule->contact_details = $request->contact_details;
        $batch_schedule->type = $request->type;
        $batch_schedule->service_package_id = $request->service_package_id;
        $batch_schedule->executive_id = $request->executive_id;
        $batch_schedule->support_stuff_id = $request->support_stuff_id;
        $batch_schedule->paper = $request->paper;
        $batch_schedule->year = $request->year;
        $batch_schedule->session_id = $request->session_id;
        $batch_schedule->institute_id = $request->institute_id;
        $batch_schedule->course_id = $request->course_id;
        $batch_schedule->batch_id = $request->batch_id[0] ?? $request->batch_id;
        $batch_schedule->initial_date = $request->initial_date;
        $batch_schedule->status = $request->status;
        $batch_schedule->updated_by = Auth::id();

        $schedule_id = $id;

        $this->updateBatchSchedulesBatches( $request->batch_id, $schedule_id );

        $batch_schedule->push();


        if ($request->wd_ids) {

            if ( BatchesSchedulesWeekDays::where('schedule_id', $schedule_id)->first() ) {
                BatchesSchedulesWeekDays::where('schedule_id', $schedule_id)->delete();
            }

            $week_day_ids = $request->wd_ids;

            if (is_array($week_day_ids)) {
                foreach ($week_day_ids as $key => $value) {
                    unset($batch_schedule_week_days);
                    $batch_schedule_week_days = new BatchesSchedulesWeekDays();
                    $batch_schedule_week_days->schedule_id = $schedule_id;
                    $batch_schedule_week_days->day_id = $value;
                    $batch_schedule_week_days->status = $request->status;
                    $batch_schedule_week_days->created_by = Auth::id();
                    $batch_schedule_week_days->save();
                    $week_insert_id = $batch_schedule_week_days->id;
                    if ($week_insert_id) $week_insert_id_status = true;
                    else $week_insert_id_status = false;

                }
            }
        }



        if ($request->slot_type) {

            if (BatchesSchedulesSlots::where('schedule_id', $schedule_id)->first()) {
                BatchesSchedulesSlots::where('schedule_id', $schedule_id)->delete();
            }

            $slot_types = $request->slot_type;

            if (is_array($slot_types)) {
                foreach ($slot_types as $key => $value) {
                    if ($value && $_POST['start_time'][$key] && $_POST['end_time'][$key]) {
                        unset($batch_schedule_slots);
                        $batch_schedule_slots = new BatchesSchedulesSlots();
                        $batch_schedule_slots->schedule_id = $schedule_id;
                        $batch_schedule_slots->slot_type = $value;
                        $batch_schedule_slots->start_time = $_POST['start_time'][$key];
                        $batch_schedule_slots->end_time = $_POST['end_time'][$key];
                        $batch_schedule_slots->status = $request->status;
                        $batch_schedule_slots->created_by = Auth::id();
                        $batch_schedule_slots->save();
                        $slot_type_insert_id = $batch_schedule_slots->id;
                        if ($slot_type_insert_id) $slot_type_insert_id_status = true;
                        else $slot_type_insert_id_status = false;
                    }

                }

            }

        }

        $this->save_subject_faculty_relation( $schedule_id, $request );

        Session::flash('message', 'Record has been updated successfully');
        return redirect()->action('Admin\BatchesSchedulesController@edit',  [$schedule_id] );

    }

    public function lectures_exams_save( $id ){

        $data['title'] = 'SIF Admin : Batches Schedules Edit';
        $data['service_packages'] = ServicePackages::get()->pluck('name', 'id');
        $data['schedule_types'] = array(''=>'Select Type','R'=>'Regular','C'=>'Crush','M'=>'Mock','E'=>'Exam');
        $data['rooms_types'] = RoomsTypes::get()->pluck('room_name', 'id');
        $data['executive_list'] = ExecutivesStuffs::get()->where('emp_type','1')->pluck('name', 'id');
        $data['support_stuff_list'] = ExecutivesStuffs::get()->where('emp_type','2')->pluck('name', 'id');
        $data['papers'] = array(''=>'Select Paper','I'=>'Paper-I','II'=>'Paper-II','III'=>'Paper-III');
        $data['years'] = array(''=>'Select year');

        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }

        $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');

        $data['week_days'] = WeekDays::pluck('name', 'id');
        $data['slots_list'] = BatchesSchedulesSlotTypes::get()->pluck('slot_name', 'slot_type');

        $schedule_details = BatchesSchedules::find($id);
        $data['schedule_details'] = $schedule_details;

        $data['topics_list'] = Topics::get()->where('course_id', $schedule_details->course_id)->sortBy('name')->pluck('name','id');

        $data['wd_ids'] = BatchesSchedulesWeekDays::get()->where('schedule_id',$id)->pluck('day_id')->toArray();
        $data['batch_slots'] = BatchesSchedulesSlots::where('schedule_id',$id)->get();
        $data['teachers_list'] = Teacher::get()->pluck('name','id');

        $data['selected_batches'] = BatchesSchedulesBatches::where( 'batch_schedule_id', $id )->pluck( 'batch_id' )->toArray();

        $is_combined = $data['schedule_details']->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID;

        $data['courses'] = Courses::get()->where('institute_id',$data['schedule_details']->institute_id)->pluck('name', 'id');


        $data['batches'] = Batches::get()->where('institute_id',$data['schedule_details']->institute_id)
            ->where('course_id',$data['schedule_details']->course_id )
            ->pluck('name', 'id');
        $course = Courses::find( $data['schedule_details']->course_id );

        if( $data['schedule_details']->institute->type==1 ){

            if( $is_combined ) {
                $data[ 'faculties' ] = $course->combined_faculties()->pluck('name', 'id');
            }else {
                $data['faculties'] = Faculty::where('course_id',$data['schedule_details']->course_id)->pluck('name', 'id');
            }

            $data['subjects'] = Subjects::where('faculty_id',$data['schedule_details']->faculty_id)->pluck('name', 'id');

            $batches_shcedules_faculties = BatchesSchedulesFaculties::where('schedule_id',$id)->get();
            $selected_faculties = array();
            foreach($batches_shcedules_faculties as $faculty)
            {
                $selected_faculties[] = $faculty->faculty_id;
            }

            $data['selected_faculties'] = collect($selected_faculties);

        }

        if( $data['schedule_details']->institute->type == 0 || $data['schedule_details']->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ){

            if( $is_combined ) {
                $data[ 'subjects' ] = $course->combined_disciplines()->pluck('name', 'id');
            }else {
                $data[ 'subjects' ] = Subjects::where('course_id',$data['schedule_details']->course_id)->pluck('name', 'id');
            }

            $batches_shcedules_subjects = BatchesSchedulesSubjects::where('schedule_id',$id)->get();
            $selected_subjects = array();
            foreach($batches_shcedules_subjects as $subject)
            {
                $selected_subjects[] = $subject->subject_id;
            }

            $data['selected_subjects'] = collect($selected_subjects);
        }

        $initial_date = $data['schedule_details']->initial_date;
        $weekdays = array('1'=>'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $array_weekday = $data['wd_ids'];


        // Create a new DateTime object
        $date = new \DateTime($initial_date);
        $dates = array();
        $j = array_search($value = $date->format('N'), $array_weekday);

        for($i = 1;$i<=count($data['topics_list']);$i++){
            // Create a new DateTime object
            $date = new \DateTime($date->format('Y-m-d'));

            if(count($array_weekday)>1){

                // Modify the date it contains
                if($i==1){
                    $dates[] = $date->modify($weekdays[$array_weekday[$j++]])->format('Y-m-d');
                    if($j > count($array_weekday)-1 )$j = 0;
                }
                else {
                    if($j == 0 || $j == count($array_weekday)-1){$dates[] = $date->modify($weekdays[$array_weekday[$j++]])->format('Y-m-d');}
                    else $dates[] = $date->modify('next '.$weekdays[$array_weekday[$j++]])->format('Y-m-d');
                    if($j > count($array_weekday)-1 )$j = 0;
                }

            }
            else if(count($array_weekday)==1){

                if($i==1){
                    $dates[] = $date->modify($weekdays[$array_weekday[$j]])->format('Y-m-d');
                }
                else {

                    $dates[] = $date->modify('next ' . $weekdays[$array_weekday[$j]])->format('Y-m-d');
                }

            }


        }

        $data['schedule_dates'] = $dates;



        return view('admin.batches_schedules.schedules_lectures_exams_save', $data);
    }

    public function save_batch_schedule_lectures_exams($schedule_id){

        if (BatchesSchedulesLecturesExams::where('schedule_id', $schedule_id)->first()) {
            BatchesSchedulesLecturesExams::where('schedule_id', $schedule_id)->delete();
        }

        if (isset($_POST['topic_id'])) {
            $topic_id = $_POST['topic_id'];
            if (is_array($topic_id)) {
                foreach ($topic_id as $keys => $values) {
                    foreach ($values as $key=>$value){

                        if ($_POST['schedule_date'][$keys][$key]) {
                            $topics = implode(",", $_POST['topic_id'][$keys][$key]);
                            unset($batches_schedules_lectures_exams);
                            $batches_schedules_lectures_exams = new BatchesSchedulesLecturesExams();
                            $batches_schedules_lectures_exams->schedule_id = $_POST['schedule_id'];
                            $batches_schedules_lectures_exams->schedule_date = $_POST['schedule_date'][$keys][$key][0];
                            $batches_schedules_lectures_exams->topic_id =$topics;
                            $batches_schedules_lectures_exams->slot_id = $_POST['slot_id'][$keys][$key][0];
                            $batches_schedules_lectures_exams->teacher_id = $_POST['teacher_id'][$keys][$key][0];
                            $batches_schedules_lectures_exams->save();

                        }

                    }
                }
            }
        }

        Session::flash('message', 'Schedule Data added successfully' );

        if(isset($_POST['submit'])){
            return redirect()->action('Admin\BatchesSchedulesController@index');
        }else{
            return redirect()->action('Admin\BatchesSchedulesController@print_batch_schedule',  [$schedule_id] );
        }

    }

    public function print_batch_schedule($schedule_id)
    {
        $data['shcedule_details'] = BatchesSchedules::find($schedule_id);
        $data['shcedule_slot_details'] = BatchesSchedulesSlots::where('schedule_id',$schedule_id)->get();
        $data['batches_lecture_exams'] = BatchesSchedulesLecturesExams::where('schedule_id',$schedule_id)->orderBy('schedule_date')->get();
        $data['lecture_exams'] = array();
        foreach ($data['batches_lecture_exams'] as $result) {
            $data['lecture_exams'][$result->schedule_date][] = $result;
        }
        $data['count_rows'] = count($data['lecture_exams']);
        $data['company'] = array('name'=>'Genesis');

        if(isset($data['count_rows'])){
            return view('admin.batches_schedules.batch_schedule_print', $data);
        }else{
            return redirect()->action('Admin\BatchesSchedulesController@edit',  [$schedule_id] );
        }



    }





    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        //dd( Auth::id() );

        if( BatchesSchedules::find($id) ) {
            BatchesSchedules::where('id', $id)->update( [ 'deleted_by' => Auth::id() ] );
        }
        BatchesSchedules::find($id)->delete();
        if (BatchesSchedulesFaculties::where('schedule_id', $id)->first()) {
            BatchesSchedulesFaculties::where('schedule_id', $id)->update( [ 'deleted_by' => Auth::id() ] );
            BatchesSchedulesFaculties::where('schedule_id', $id)->delete();
        }
        if (BatchesSchedulesSubjects::where('schedule_id', $id)->first()) {
            BatchesSchedulesSubjects::where('schedule_id', $id)->update( [ 'deleted_by' => Auth::id() ] );
            BatchesSchedulesSubjects::where('schedule_id', $id)->delete();
        }
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->back();
    }





}
