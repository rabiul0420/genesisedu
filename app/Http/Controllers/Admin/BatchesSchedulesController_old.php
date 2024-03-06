<?php

namespace App\Http\Controllers\Admin;

use App\BatchesSchedulesLecturesExams;
use App\BatchesSchedulesSlots;
use App\BatchesSchedulesSlotTypes;
use App\BatchesSchedulesSubjects;
use App\BatchesSchedulesWeekDays;
use App\ExecutivesStuffs;
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
use App\Teachers;
use App\Faculty;
use App\Subject;
use App\Batches;
use App\Doctors;
use App\Sessions;
use App\Service_packages;
use App\ServicePackages;
use App\BatchesSchedules;
use Session;
use Auth;
use Validator;
use Illuminate\Support\Facades\DB;


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
        /*echo '<pre>';
        echo print_r('Bismillah');exit;*/
        $batches_schedules = BatchesSchedules::get();
        $title = 'SIF Admin : Batches Schedules List';
        return view('admin.batches_schedules.list',['batches_schedules'=>$batches_schedules,'title'=>$title]);
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
        for($year = date("Y")+1;$year>=2017;$year--){
            $data['years'][$year] = $year;
        }
        $data['sessions'] = Sessions::get()->pluck('name', 'id');
        $data['institutes'] = Institutes::get()->pluck('name', 'id');
        //$data['courses'] = Courses::get()->pluck('name', 'id');
        //$data['faculties'] = Faculty::get()->pluck('name', 'id');
        //$data['subjects'] = Subject::get()->pluck('name', 'id');
        //$data['batches'] = Batches::get()->pluck('name', 'id');
        $data['week_days'] = WeekDays::pluck('name', 'id');
        $data['slots_list'] = BatchesSchedulesSlotTypes::get()->pluck('slot_name', 'slot_type');

        return view('admin.batches_schedules.create',$data);
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
        $batch_schedule->batch_id = $request->batch_id;
        $batch_schedule->initial_date = $request->initial_date;
        $batch_schedule->status = $request->status;
        $batch_schedule->created_by = Auth::id();

        $batch_schedule->save();
        $schedule_id = $batch_schedule->id;

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



        Session::flash('message', 'Record has been added successfully');

        return redirect()->action('Admin\BatchesSchedulesController@lectures_exams_save',  [$schedule_id] );

        /*return redirect()->action('Admin\BatchesSchedulesController@lectures_exams_save', [$schedule_id] );*/

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
        //$data['courses'] = Courses::get()->where('institute_id',)->pluck('name', 'id');
        //$data['faculties'] = Faculty::get()->pluck('name', 'id');
        //$data['subjects'] = Subject::get()->pluck('name', 'id');
        //$data['batches'] = Batches::get()->pluck('name', 'id');
        $data['week_days'] = WeekDays::pluck('name', 'id');
        $data['slots_list'] = BatchesSchedulesSlotTypes::get()->pluck('slot_name', 'slot_type');

        $data['schedule_details'] = BatchesSchedules::find($id);
        $data['topics_list']= Topics::get()->where('course_id',BatchesSchedules::find($id)->course_id)->sortBy('name')->pluck('name','id');
        //$data['wd_ids'] = WeekDays::get()->whereIn('wd_id',BatchesSchedulesWeekDays::get()->where('schedule_id',$id)->pluck('day_id'))->pluck('wd_id');
        //$data['batch_slots'] = BatchesSchedulesSlotTypes::get()->whereIn('slot_type',BatchesSchedulesSlots::get()->where('schedule_id',$id)->pluck('slot_type'))->pluck('slot_type');
        $data['wd_ids'] = BatchesSchedulesWeekDays::get()->where('schedule_id',$id)->pluck('day_id')->toArray();
        $data['batch_slots'] = BatchesSchedulesSlots::where('schedule_id',$id)->get();
        $data['teachers_list'] = Teachers::get()->pluck('tec_name','bmdc_no');

        $data['courses'] = Courses::get()->where('institute_id',$data['schedule_details']->institute_id)->pluck('name', 'id');
        $data['batches'] = Batches::get()->where('institute_id',$data['schedule_details']->institute_id)
            ->where('course_id',$data['schedule_details']->course_id)
            ->pluck('name', 'id');

        $initial_date = $data['schedule_details']->initial_date;
        $weekdays = array('1'=>'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $array_weekday = $data['wd_ids'];



        // Create a new DateTime object
        $date = new \DateTime($initial_date);
        $dates = array();
        $j = array_search($value = $date->format('N'), $array_weekday);

        //echo '<pre>';print_r($weekdays[$array_weekday[$j++]]);exit;

        /*echo '<pre>';
        print_r($date->modify($array_weekday[$j])->format('N'));exit;*/

        for($i = 1;$i<=count($data['topics_list']);$i++){
            // Create a new DateTime object
            $date = new \DateTime($date->format('Y-m-d'));

            if(count($array_weekday)>1){

                // Modify the date it contains
                if($i==1){
                    $dates[] = $date->modify($weekdays[$array_weekday[$j++]])->format('Y-m-d');
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

        /*echo '<pre>';
        print_r($dates);exit;*/

        //return view('admin.batches_schedules.schedules_lectures_exams_save', $data);


        return view('admin.batches_schedules.edit', $data);
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
        $batch_schedule->batch_id = $request->batch_id;
        $batch_schedule->initial_date = $request->initial_date;
        $batch_schedule->status = $request->status;
        $batch_schedule->updated_by = Auth::id();

        $batch_schedule->push();

        $schedule_id = $id;

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

        Session::flash('message', 'Record has been updated successfully');

        return redirect()->action('Admin\BatchesSchedulesController@edit',  [$schedule_id] );

        //return back();

    }

    public function lectures_exams_save($id){

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

        $data['schedule_details'] = BatchesSchedules::find($id);
        $data['topics_list']= Topics::get()->where('course_id',BatchesSchedules::find($id)->course_id)->sortBy('name')->pluck('name','id');

        $data['wd_ids'] = BatchesSchedulesWeekDays::get()->where('schedule_id',$id)->pluck('day_id')->toArray();
        $data['batch_slots'] = BatchesSchedulesSlots::where('schedule_id',$id)->get();
        $data['teachers_list'] = Teachers::get()->pluck('tec_name','bmdc_no');

        $data['courses'] = Courses::get()->where('institute_id',$data['schedule_details']->institute_id)->pluck('name', 'id');
        $data['batches'] = Batches::get()->where('institute_id',$data['schedule_details']->institute_id)
                                         ->where('course_id',$data['schedule_details']->course_id)
                                         ->pluck('name', 'id');

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

        if ($_POST['topic_id']) {
            $topic_id = $_POST['topic_id'];
            if (is_array($topic_id)) {
                $i=1;
                foreach ($topic_id as $key => $value) {

                    if($_POST['slot_id'][$key]==2){
                        $exam_topics = implode (", ", $_POST['examtopic_'.$i]);
                        if($_POST['schedule_date'][$key]) {
                            unset($batches_schedules_lectures_exams);
                            $batches_schedules_lectures_exams = new BatchesSchedulesLecturesExams();
                            $batches_schedules_lectures_exams->schedule_id = $_POST['schedule_id'];
                            $batches_schedules_lectures_exams->schedule_date = $_POST['schedule_date'][$key];
                            $batches_schedules_lectures_exams->topic_id = $exam_topics;
                            $batches_schedules_lectures_exams->slot_id = $_POST['slot_id'][$key];
                            $batches_schedules_lectures_exams->teacher_id = $_POST['teacher_id'][$key];
                            $batches_schedules_lectures_exams->save();

                        }
                        $i++;
                    }else{
                        if($_POST['schedule_date'][$key]) {
                            unset($batches_schedules_lectures_exams);
                            $batches_schedules_lectures_exams = new BatchesSchedulesLecturesExams();
                            $batches_schedules_lectures_exams->schedule_id = $_POST['schedule_id'];
                            $batches_schedules_lectures_exams->schedule_date = $_POST['schedule_date'][$key];
                            $batches_schedules_lectures_exams->topic_id = $_POST['topic_id'][$key];
                            $batches_schedules_lectures_exams->slot_id = $_POST['slot_id'][$key];
                            $batches_schedules_lectures_exams->teacher_id = $_POST['teacher_id'][$key];
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

    public function print_batch_schedule($schedule_id){

        $data['shcedule_details'] = BatchesSchedules::find($schedule_id);
        $data['shcedule_slot_details'] = BatchesSchedulesSlots::where('schedule_id',$schedule_id)->get();
        $data['batches_lecture_exams'] = BatchesSchedulesLecturesExams::where('schedule_id',$schedule_id)->get();

        foreach ($data['batches_lecture_exams'] as $result) {
            $data['lecture_exams'][$result->schedule_date][] = $result;
        }

        $data['count_rows'] = count($data['lecture_exams']);
        $data['company'] = array('name'=>'Genesis');

        //echo '<pre>';print_r($data['lecture_exams']);exit;

        return view('admin.batches_schedules.batch_schedule_print', $data);

    }





    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        /*$user=BatchesSchedules::find(Auth::id());

        if(!$user->hasRole('Admin')){
            return abort(404);
        }*/

        BatchesSchedules::destroy($id); // 1 way
        Session::flash('message', 'Record has been deleted successfully');
        return redirect()->action('Admin\BatchesSchedulesController@index');
    }





}