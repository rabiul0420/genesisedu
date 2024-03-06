<?php

namespace App\Http\Controllers\Admin;

use App\BatchesSchedulesBatches;
use App\BatchesSchedulesLecturesExams;
use App\BatchesSchedulesMeta;
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
use App\Http\Resources\BatchesSchedulesResource;
use App\Http\Traits\ContentSelector;
use App\LectureVideo;
use App\Providers\AppServiceProvider;
use App\ScheduleDetail;
use App\ScheduleTimeSlot;
use App\Subjects;
use App\TopicFaculty;
use App\Topics;
use App\TopicSubject;
use App\WeekDays;
use App\Coming_by;
use App\ComingBy;
use App\Http\Controllers\Controller;

use App\MedicalColleges;
use App\RoomsTypes;
use http\Env\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
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
use App\DoctorClassView;
use App\User;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Session;
use Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BatchSchedulesControllerV2 extends Controller
{

    use ContentSelector;

    /**
     * Show the schedule list
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $batches_schedules = BatchesSchedules::with('time_slots')
            ->whereHas('time_slots')->get();

        $title = 'SIF Admin : Batches Schedules List';
        $batches = Batches::get()->pluck('name', 'id');
        $courses = Courses::get()->pluck('name', 'id');
        $sessions = Sessions::get()->pluck('name', 'id');
        $years = array('' => 'Select year');
        for ($year = date("Y"); $year >= 2017; $year--) {
            $years[$year] = $year;
        }
        return view('admin.batch_schedules.list', ['batches_schedules' => $batches_schedules, 'title' => $title, 'courses' => $courses, 'batches' => $batches, 'years' => $years, 'sessions' => $sessions]);
    }

    protected function selection_config( )
    {
        return [
            'institutes' => [
                'label_column_count' => 3,
                'column_count' => 3,
                'label' => 'Institute',
            ],
            'courses' => [
                'label_column_count' => 3,
                'column_count' => 3,
                'label' => 'Course',
            ],
            'sessions' => [
                'label_column_count' => 3,
                'column_count' => 3,
                'label' => 'Session',
            ],
            'batches' => [
                'label_column_count' => 3,
                'column_count' => 3,
                'label' => 'Batch',
            ]

        ];
    }

    public function doctors_feedback()
    {
        return view('admin.batch_schedules.feedback_list');
    }

    protected function calculate_ratings( $doctor_class_views, &$total_ratings = 0 )
    {
        if ( $doctor_class_views instanceof Collection ) {
            $total_ratings = $doctor_class_views->count();

            $output = [];

            foreach ($doctor_class_views as $view) {
                if (!empty($view->ratings)) {

                    $all_ratings = json_decode($view->ratings, true);

                    if (is_array($all_ratings)) {
                        foreach ($all_ratings as $variant => $ratings) {
                            $output[$variant] = $output[$variant] ?? [];

                            if (is_array($ratings)) {
                                foreach ($ratings as $criteria => $rating) {
                                    $output[$variant][$criteria] = $output[$variant][$criteria] ?? [];
                                    $output[$variant][$criteria][$rating] = $output[$variant][$criteria][$rating] ?? 0;
                                    if (is_int($output[$variant][$criteria][$rating])) {
                                        $output[$variant][$criteria][$rating]++;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return  $output;
        }
    }

    public function doctors_ratings(Request $request)
    {

        $lecture_video = LectureVideo::pluck('name', 'id');
        $mentors = Teacher::pluck('name', 'id');

        $ratings_details = ScheduleDetail::with(['doctor_class_views' => function ($query) {
            $query->whereNotNull('ratings');
        }, 'video', 'mentor', 'time_slot.schedule']);

        $ratings_details->whereExists(function ($views) {
            $views->from('doctor_class_views')
                ->whereRaw('lecture_video_id = schedule_details.class_or_exam_id')
                ->whereNotNull('ratings');
        });

        if ($request->lecture_video_id) {
            $ratings_details->where('class_or_exam_id', $request->lecture_video_id);
        }

        if ($request->mentor_id) {
            $ratings_details->where('mentor_id', $request->mentor_id);
        }


        $schedule_detials = $ratings_details->get();
        //        calculate_ratings( $schedule_detials->get(4)->doctor_class_views );



        $schedule_detials->each(function (&$detail) {

            if ($detail->doctor_class_views instanceof Collection) {
                $detail->rating_count = $detail->doctor_class_views->count();
                $detail->rating_calculation = $this->calculate_ratings($detail->doctor_class_views);
            }
        });

        //        return $schedule_detials;


        return view('admin.batch_schedules.doctors-ratings', compact('lecture_video', 'schedule_detials', 'mentors'));
    }

    public function doctors_feedback_list( Request $request )
    {

        $doctor_feedback = DB::table('schedule_details as sd')
            ->join('schedule_time_slots as sts', 'sts.id', 'sd.slot_id')
            ->join('batches_schedules as bs', 'bs.id', 'sts.schedule_id')
            ->join('batches_schedules as bs', 'bs.id', 'sts.schedule_id')
            ->join('lecture_video as lv', 'lv.id', 'sd.class_or_exam_id')
            ->join('doctor_class_views as dcv', 'dcv.lecture_video_id', 'lv.id')
            ->join('doctors_courses as dc', 'dc.id', 'dcv.doctor_course_id')
            ->join('doctors as d', 'dc.doctor_id', 'd.id')
            ->join('teacher as t', 'sd.mentor_id', 't.id')
            ->where('sd.type', 'Class')
            ->whereNull('sd.deleted_at')
            ->whereNull('sts.deleted_at')
            ->groupBy('dcv.id');

        $doctor_feedback = $doctor_feedback->select([
            't.name as teacher_name',
            't.designation as teacher_designation',
            'lv.id as id',
            'd.name as doctor_name',
            'd.bmdc_no as bmdc_no',
            'lv.name as lecture_video',
            'dcv.feedback as feedback',
            'dcv.ratings as ratings'
        ]);

        return Datatables::of($doctor_feedback)
            ->addColumn('teacher', function ($doctor_feedback) {
                return '<div>' . $doctor_feedback->teacher_name . '</div>
                        <div>' . $doctor_feedback->teacher_designation . '</div>';
            })
            ->rawColumns(['teacher'])
            ->make(true);
    }

    public function batch_schedule_list(Request $request)
    {

        $year = $request->year;
        $course_id = $request->course_id;
        $session_id = $request->session_id;
        $batch_id = $request->batch_id;
        $batch_schedule_list = DB::table('batches_schedules as d1')
            ->join('schedule_time_slots as sts', 'sts.schedule_id', '=', 'd1.id')
            ->leftjoin('courses as d2', 'd1.course_id', '=', 'd2.id')
            ->leftjoin('service_packages as d3', 'd1.service_package_id', '=', 'd3.id')
            ->leftjoin('batches as d4', 'd1.batch_id', '=', 'd4.id')
            ->leftjoin('sessions as d5', 'd1.session_id', '=', 'd5.id')
            ->leftjoin('faculties as f', 'd1.faculty_id', 'f.id')
            ->leftjoin('subjects as s', 'd1.subject_id', 's.id')
            ->leftjoin('subjects as bs', 'd1.bcps_subject_id', 'bs.id')
            ->whereNull('d1.deleted_at')
            ->groupBy('d1.id');


        if ($year) {
            $batch_schedule_list = $batch_schedule_list->where('d1.year', '=', $year);
        }
        if ($course_id) {
            $batch_schedule_list = $batch_schedule_list->where('d1.course_id', '=', $course_id);
        }
        if ($session_id) {
            $batch_schedule_list = $batch_schedule_list->where('d1.session_id', '=', $session_id);
        }
        if ($batch_id) {
            $batch_schedule_list = $batch_schedule_list->where('d1.batch_id', '=', $batch_id);
        }

        $batch_schedule_list = $batch_schedule_list->select(
            'd1.id as id',
            'd1.name as batch_schedule',
            'd1.year as year',
            'd1.type as schedul_type',
            'd2.name as course_name',
            'd3.name as name',
            'd4.name as batch_name',
            'f.name as faculty_name',
            's.name as subject_name',
            'bs.name as bcps_subject_name',
            'd5.name as session_name',
            'd1.deleted_at'
        );

        return Datatables::of($batch_schedule_list )
            ->addColumn('action', function ($batch_schedule_list ) {
                return view('admin.batch_schedules.ajax_list', (['batch_schedule_list' => $batch_schedule_list]));
            })
            ->make(true);
    }

    private function schedule_data($id, $action = 'edit')
    {

        $relations = [

            'time_slots' => function ($slot) use ($action) {
                return $slot->whereNull('deleted_at')->orderBy('datetime');
            },

            'time_slots.schedule_details' => function ($details)  use ($action) {

                if ($action == 'duplicate') {

                    $details->select(
                        'type',
                        'class_or_exam_id',
                        'slot_id',
                        'mentor_id',
                        'priority',
                        'id',
                        'parent_id',
                        DB::raw('concat( "duplicate-", id ) as dup_id'),
                        DB::raw('concat( "duplicate-", parent_id ) as dup_parent_id')

                    );
                }

                return $details->orderBy('priority');
            },

            'meta' => function ($meta) {
                return $meta->where('key', 'fb_links');
            }
        ];

        if( $action == 'view' || $action == 'duplicate' ) {

            $relations[ 'time_slots.schedule_details' ] = function( $detail ){
                return $detail->where( 'parent_id', 0 )->orderBy( 'priority' );
            };

            $relations[] =  'time_slots.schedule_details.exam.question_type';
            $relations[] =  'time_slots.schedule_details.lectures';
            $relations[] =  'time_slots.schedule_details.video';
            $relations[] =  'time_slots.schedule_details.mentor';
            $relations[] =  'time_slots.schedule_details.lectures.video';
            $relations[] =  'time_slots.schedule_details.lectures.exam.question_type';
            $relations[] =  'time_slots.schedule_details.lectures.mentor';
            $relations[] =  'room';
        }

        $data = BatchesSchedules::with( $relations )->find($id);

        return $data;
    }

    public function create( Request  $request)
    {
        $data = [];


        $data = $this->data_( $request, 0, 'create' );

        $data['schedule'] = new BatchesSchedules();
        $data['id'] = 0;

        return view('admin.batch_schedules.form', $data);
    }

    public function edit( Request $request, $id )
    {
        $data = $this->data_( $request, $id, 'edit' );

        $data['id'] = $id;

        $batch_schedule = $this->schedule_data( $id, 'view' );


        //BatchesSchedulesResource::withoutWrapping( );

//        $data['schedule'] = new BatchesSchedulesResource($batch_schedule);
        $data['schedule'] = $batch_schedule;

        return view('admin.batch_schedules.form', $data);
    }

    public function duplicate(Request $request, $id)
    {
        $data = $this->data_( $request, $id, 'duplicate' );

        $data['id'] = $id;
        $batch_schedule = $this->schedule_data($id, 'duplicate');
        $batch_schedule->faculty_id = null;
        $batch_schedule->subject_id = null;
        $batch_schedule->bcps_subject_id = null;


        $data[ 'schedule' ] = $batch_schedule;

        return view('admin.batch_schedules.form', $data);
    }

    public function save(Request $request, $id = 0)
    {


//        return $request->all();

        $isEdit = $request->method() == 'PUT';
        $batch_schedule = $isEdit ? BatchesSchedules::find($id) : new BatchesSchedules();

        $old = $batch_schedule;

        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', Rule::unique('batches_schedules', 'name')->whereNull('deleted_at')
                    ->ignore($batch_schedule->id)],
                'status' => ['required'],
                'contact_details' => ['required'],
                'executive_id' => ['required'],
                'terms_and_condition' => ['required'],
                'institute_id' => ['required'],
                'course_id' => ['required'],
                'session_id' => ['required'],
                'year' => ['required'],
                'batch_id' => ['required'],
                'room_id' => ['required'],
                'address' => ['required'],
            ],
            [
                'batch_id.unique' => 'This batch already exists'
            ]
        );

        if ( $validator->fails( ) ) {
            Session::flash('class', 'alert-danger');
            Session::flash('message', 'Please provide correct input!');
            Session::flash('validation_errors', $validator->errors()->getMessages());

            return back()->withInput()->withErrors( $validator );
        }

        //DB::enableQueryLog();

        $batch = Batches::with('institute','course')->find($request->batch_id ?? 0);
        $batchData = BatchesSchedules::where('batch_id', $request->batch_id)->whereNull('deleted_at');
        $batchMessage = "<em><b>" . ucfirst($batch->name) . "</b></em> batch already exists ";

        if ($isEdit) {
            $batchData->where('batch_id', '!=', $batch_schedule->batch_id);
        }

        if ($batch) {

            if ($batch->fee_type != 'Faculty_Or_Discipline' || $batch->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {



                if( $batch->institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

                    $batchMessage .= " with <em><b>" . Faculty::where('id', $request->faculty_id)->value( 'name' )
                        . ' faculty</b></em> and <em><b>' . Subjects::where('id', ($request->bcps_discipline_id ?? $request->bcps_subject_id) )->value('name') . '</b></em> discipline'  ;

                    $batchData->where( 'faculty_id', $request->faculty_id );
                    $batchData->where( 'bcps_subject_id', ($request->bcps_discipline_id ?? $request->bcps_subject_id) );

                } else {
                    if ($batch->institute->type == 1) {
                        $batchMessage .= " with <em><b>" . Faculty::where('id', $request->faculty_id)->value('name') . '</b></em> faculty';
                        $batchData->where('faculty_id', $request->faculty_id);
                    } else {
                        $batchMessage .= " with  <em><b>" . Subjects::where('id', $request->subject_id)->value('name') . '</b></em> discipline';
                        $batchData->where('subject_id', $request->subject_id);
                    }
                }

            }

        }

        //$batchData->exists();

        //return DB::getQueryLog();

        if ($batchData->exists()) {

            Session::flash('class', 'alert-danger');
            Session::flash('message', $batchMessage . '.');

            return back()->withInput();
        }

        $batch_schedule->name = $request->name;
        $batch_schedule->tag_line = $request->tag_line;
        $batch_schedule->contact_details = $request->contact_details;
        $batch_schedule->executive_id =  $request->executive_id;
        $batch_schedule->terms_and_condition = $request->terms_and_condition;
        $batch_schedule->year = $request->year;
        $batch_schedule->session_id = $request->session_id;
        $batch_schedule->institute_id = $request->institute_id;
        $batch_schedule->course_id = $request->course_id;
        $batch_schedule->batch_id = $request->batch_id;
        $batch_schedule->faculty_id = $request->faculty_id ?? null;
        $batch_schedule->subject_id = $request->subject_id ?? null;
        $batch_schedule->status = $request->status === null ?  1 : $request->status;
        $batch_schedule->bcps_subject_id = $request->bcps_subject_id ?? null;
        $batch_schedule->room_id = $request->room_id ?? null;
        $batch_schedule->address = $request->address ?? null;

        if (!$isEdit) {
            $batch_schedule->created_by = Auth::id();
        } else {
            $batch_schedule->updated_by = Auth::id( );
        }

        $batch_schedule->save();

        $this->save_fb_links($batch_schedule, $request);



//        return $request->details;

        if ( is_array( $request->details ) ) {

            $inserted_slot_ids = [];

            foreach ($request->details as $detail) {

                $slot_id = ($isEdit) ? (isset($detail['slot_id']) && is_numeric($detail['slot_id']) ? $detail['slot_id'] : 0) : null;

                //$slot = ScheduleTimeSlot::withTrashed()->find($slot_id) ?? new ScheduleTimeSlot();

                $date = $detail['date'] ?? '';
                $time = $detail['time'] ?? '';

                $datetime = Carbon::make($date . ' ' . $time)->format('Y-m-d H:i:s');

                $where = $slot_id
                    ? [ 'id' => $slot_id, 'schedule_id'   => $batch_schedule->id ]
                    : [
                        'datetime'      => $datetime,
                        'schedule_id'   => $batch_schedule->id,
                    ];

                $data = $slot_id
                    ? [
                        'datetime' => $datetime
                    ]
                    : [
                        'deleted_at' => null,
                        'deleted_by' => null
                    ];

                $slot = ScheduleTimeSlot::withTrashed( )->limit(1)->updateOrCreate( $where, $data );

                $contents = $detail['contents'] ?? [];
                $priority = 0;

                foreach ( ( $contents ?? [] ) as $content ) {

                    if (isset($content['type'])) {

                        $detail_id = ($isEdit) ? (isset($content['id']) && is_numeric($content['id']) ? $content['id'] : 0) : null;



                        $updatedContent = $this->updateContentData( $slot, $content, $priority, $detail_id );

                        //return [ $content['children']->class_or_exam_id, $updatedContent ];

                        if( ($updatedContent->id ?? 0 ) > 0 &&  ( $content['children'] ?? false ) ) {

                            $childIds = $content['children']['id'] ?? null;
                            $childMentorIds = $content['children']['mentor_id'] ?? null;

                            $childPriority = 0;

                            foreach( ( $content['children']['class_or_exam_id'] ?? [] ) as $index => $child_class_or_exam_id ) {
                                $childContent = [
                                    'type' => 'Class',
                                    'class_or_exam_id' => $child_class_or_exam_id,
                                    'mentor_id' => $childMentorIds[$index] ?? null,
                                ];

                                $childDetailId = $childIds[$index] ?? null;
                                $childMentorId = $childMentorIds[$index] ?? null;

                                $this->updateContentData( $slot, $childContent, $childPriority, $childDetailId, $updatedContent->id  );
                                $childPriority++;
                            }

                        }

                    }


                    $priority++;

                }
            }
        }

        Session::flash( 'message', 'Record has been added successfully' );
        return redirect()->action('Admin\BatchSchedulesControllerV2@edit',  [$batch_schedule->id]);
    }

    protected function updateContentData( $slot, $content, $priority, $detail_id  = null, $parent_id = null ){
        $contentWhere =
            $detail_id
                ? [
                'id' => $detail_id,
                'slot_id' => $slot->id,
            ]

            :[
                'type' => $content['type'],
                'class_or_exam_id' => $content['class_or_exam_id'],
                'mentor_id' => $content['mentor_id'],
                'slot_id' => $slot->id,
            ];

        if( $parent_id !== null && $parent_id > 0 ){
            $contentWhere['parent_id'] = $parent_id;
        }

        $contentData = $detail_id
            ?
            [
                'type' => $content['type'],
                'class_or_exam_id' => $content['class_or_exam_id'],
                'mentor_id' => $content['mentor_id'],
            ]
            : [
                'priority' => $priority,
                'deleted_at' => null,
                'deleted_by' => null
            ];

        return ScheduleDetail::withTrashed()->limit(1)->updateOrCreate( $contentWhere, $contentData );
    }


    public function remove_slot(Request $request, $id = 0 )
    {
        if( $id ) {
            if (ScheduleTimeSlot::where('id', $id)->select('id')
                ->update(['deleted_by' => Auth::id(), 'deleted_at' => date('Y-m-d H:i:s')])) {
                return response(['success' => true]);
            }
        }

        return response([ 'success' => false ] );
    }

    public function remove_class_or_exam(Request $request, $id = 0)
    {
        if( $id ) {
            if( ScheduleDetail::where( 'id', $id )->select( 'id' )
                ->update([ 'deleted_by' => Auth::id( ), 'deleted_at' => date('Y-m-d H:i:s') ]) ) {
                return response(['success' => true ]);
            }
        }

        return response([ 'success' => false ] );
    }

    public function show($id)
    {
        $data = [];
        $data['action'] = 'edit';
        $data['id'] = $id;
        $batch_schedule = $this->schedule_data($id, 'view');
        $data['batch_schedule'] = new BatchesSchedulesResource($batch_schedule);

        return view('admin.batch_schedules.show', $data );
    }

    private function topic_relations($content_type, $relation, &$rels = [])
    {

        $rels[$relation] = function ($content) use ($content_type) {

            if (request()->has('term')) {
                $text = request()->term;
                $content->where('name', 'like', '%' . $text . '%')->select('name', 'id', 'class_id');
            }

            if ($content_type == 'Class') {
                $video_type =  $_GET['video_type'] ?? '';
                $content->where('type', $video_type);
            }
        };
    }

    private function topicWhereHasCallback($content_type)
    {
        return function ($query) use ($content_type) {
            if (request()->has('term')) {
                $text = request()->term;
                $query->where('name', 'like', '%' . $text . '%')->select('name', 'id', 'class_id');
            }

            if ($content_type == 'Class') {
                $video_type =  $_GET['video_type'] ?? '';
                $query->where('type', $video_type);
            }
        };
    }

    public function lectures_exams_mentors( Request  $request )
    {

        $mentors = Teacher::get(['name', 'id']);

        $topics_with_lectures = $this->_lectures_exams(

            $request->all([
                'content_type',
                'type',
                'year',
                'session_id',
                'institute_id',
                'course_id',
                'term',
                'content_type',
                'faculty_id',
                'subject_id',
                'bcps_subject_id',
            ])

        );

        return response(['topics' => $topics_with_lectures, 'mentors' => ($mentors ?? [])]);
    }

    public function lectures_exams(Request  $request)
    {
        $topics_with_lectures = $this->_lectures_exams(
            $request->all([
                'content_type',
                'type',
                'batch_id',
                'year',
                'session_id',
                'institute_id',
                'course_id',
                'term',
                'content_type',
                'faculty_id',
                'subject_id',
                'bcps_subject_id',
            ])
        );

        return response([ 'topics' => $topics_with_lectures ]);
    }

    public function _lectures_exams($input)
    {

        $content_type =  $input['content_type'] ?? '';

        $relation = $content_type == 'Exam' ? 'exams' : ($content_type == 'Class' ? 'lectures' : ($input['relation'] ?? null));

        // return $relation;

        $contentWhere = function ( &$query ) use ( $relation ){
            $title = request()->term['term'] ?? '';
            if( $title ) { $query->where( 'name', 'LIKE', '%' . $title . '%' ); }
            if( $relation == 'lectures' ) {
                $classType = request( )->class_type ?? 1;
                $query->where( 'type', $classType );
            }

            return $query;
        };

        if ( $relation ) {

            $topics_with_lectures = Topics::with([$relation => function ($query) use( $relation, $contentWhere ) {
                if( $relation == "lectures" ) {
                    $query->select('name as text', 'id', 'class_id', 'type', 'lecture_address');
                }else {
                    $query->select('name as text', 'id', 'class_id', 'status' );
                }
                $contentWhere( $query );
            }]);

        } else {

            $topics_with_lectures = Topics::with([
                'lectures' => function ($lec) {
                    $lec->select('name as text', 'id', 'class_id', 'type', 'lecture_address');
                },
                'exams' => function ($ex) {
                    $ex->select('name as text', 'id', 'class_id', 'status' );
                }
            ]);
        }

        $topics_with_lectures->where(function ($topics_with_lectures) use ( $relation, $contentWhere ){


            $examsExists = function ($query) use ( $contentWhere ) {
                $query->select('*')->from('exam')->whereRaw('class_id = topics.id');
                $contentWhere( $query );
            };

            $lecturesExists = function ($query)  use ( $contentWhere ){
                $query->select('*')->from('lecture_video')->whereRaw('class_id = topics.id');
                $contentWhere( $query );
            };

            switch ( $relation ) {
                case 'exams':
                    $topics_with_lectures->whereExists( $examsExists );
                    break;
                case 'lectures':
                    $topics_with_lectures->whereExists( $lecturesExists );
                    break;
                default:
                $topics_with_lectures->whereExists( $examsExists );
                $topics_with_lectures->orWhereExists( $lecturesExists );
            }

        });

        $year = (int) $input['year'] ?? '';
        $session_id = (int) $input['session_id'] ?? '';
        $institute_id = (int) $input['institute_id'] ?? '';
        $course_id = (int) $input['course_id'] ?? '';


        $topics_with_lectures->where([
            'year' =>  $year,
            'session_id' => $session_id,
            'institute_id' => $institute_id,
            'course_id' => $course_id,
        ]);

        $batch = Batches::with('course.institute')->find( $input['batch_id'] ?? '');

        $institute = $batch->course->institute ?? null;

        if( ($batch->fee_type ?? '') == 'Discipline_Or_Faculty' || ( ( $institute->id ?? '') == AppServiceProvider::$COMBINED_INSTITUTE_ID ) ) {

            if ( (int)( $institute->type ?? null ) === 0 ) {
                $topics_with_lectures->whereIn(
                    'id',
                    TopicSubject::where('subject_id', $input['bcps_subject_id'])
                        ->main()->withoutTrashed()->select('topic_id')
                );
            } else  {

                $topics_with_lectures->where(function ($topics_with_lectures) use ($input) {
                    $topics_with_lectures->whereIn(
                        'id',
                        TopicFaculty::where('faculty_id', $input['faculty_id'])
                            ->withoutTrashed()->select('topic_id')
                    );

                    if ( ( $institute->id ?? '') == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {
                        $topics_with_lectures->orWhereIn(
                            'id',
                            TopicSubject::where( 'subject_id', $input['bcps_subject_id'] )
                                ->combinedBcps()->withoutTrashed()->select('topic_id')
                        );
                    }

                });
            }

        }

        $course_name = Courses::where('id', $course_id)->value('name');
        $session_name = Sessions::where('id', $session_id)->value('name');

        $text_as_html = "
            <div>
                <span class='btn btn-xs btn-primary'>{$year}</span>
                <span class='btn btn-xs btn-primary'>{$course_name}</span>
                <span class='btn btn-xs btn-primary'>{$session_name}</span>
            </div>
        ";

        $concat_after_topic_name = str_replace("'", "\'", "$text_as_html");

        return $topics_with_lectures = $topics_with_lectures->get([DB::raw("CONCAT(name, '{$concat_after_topic_name}') as name"), 'id']);
    }

    public function data_( Request $request, $schedule_id  = null, $action = 'create' )
    {
        $data = [];

        $data['action'] = $action;

        $ajaxScheduleId = $schedule_id !== null;
        $schedule_id  =  $schedule_id ?? $request->schedule_id;
        $schedule = BatchesSchedules::find($schedule_id);

        $data['title'] = 'SIF Admin : Batches Schedules Create';
        $data['rooms_types'] = RoomsTypes::pluck( 'room_name', 'id' )->prepend( '--select--', '' );
        $data['executive_list'] = ExecutivesStuffs::where('emp_type', '1')->pluck( 'name', 'id' )->prepend( '--select--', '' );
        $data['support_stuff_list'] = ExecutivesStuffs::where('emp_type', '2')->pluck( 'name', 'id' );
        $data['mentors'] = Teacher::get(['name', 'id']);


        $data['years'] = [];

        for ($year = date("Y") + 1; $year >= 2017; $year--) {
            $data['years'][$year] = $year;
        }

        $sessionRelationCallback = function ($session) {
            $session->where('sessions.status', 1)
                ->select('sessions.id', 'course_session.session_id', 'course_session.course_id', 'sessions.name', 'sessions.status');
        };

        if( $ajaxScheduleId ) {

            $data[ 'institutes_view' ] = $this->institutes( $request, ($schedule->institute_id ?? 0) )->render( );
            $data[ 'courses_view' ] = $this->courses( $request,( $schedule->course_id ?? 0 ),( $schedule->institute_id ?? 0 ) )->render( );
            $data[ 'sessions_view' ] = $this->sessions( $request,($schedule->session_id ?? 0 ) ,($schedule->course_id ?? 0 ), ( $schedule->year ?? '') )->render( );
            $data[ 'batches_view' ] = $this->batches( $request,($schedule->batch_id ?? 0 ) ,[
                'year' => $schedule->year ?? '',
                'institute_id' => $schedule->institute_id ?? '',
                'course_id' => $schedule->course_id ?? '',
                'session_id' => $schedule->session_id ?? '',
            ] )->render( );

            $data[ 'faculties_view' ] = $this->faculties( $request,($schedule->faculty_id ?? 0 ) ,[
                'institute_id' => $schedule->institute_id ?? '',
                'course_id' => $schedule->course_id ?? '',
                'batch_id' => $schedule->batch_id ?? '',
            ] )->render( );

            $data[ 'bcps_discipline_view' ] = $this->bcps_disciplines( $request,( $schedule->subject_id ?? ($schedule->bcps_subject_id ?? '') ) ,[
                'institute_id' => $schedule->institute_id ?? '',
                'course_id' => $schedule->course_id ?? '',
                'batch_id' => $schedule->batch_id ?? '',
            ] )->render( );

        }


        if ($schedule) {

            $data['batches'] = $this->_batches([
                'year' => $schedule->year ?? '',
                'institute_id' => $schedule->institute_id ?? '',
                'course_id' => $schedule->course_id ?? '',
            ]);

            $schedule_meta = $schedule->meta;
            $links = $schedule_meta->where( 'key', 'fb_links' )->first();

            if ( $links ) {
                $data['fb_links'] = json_decode($links->value, true);
            }

            //$data[ 'batches' ] = Batches::where( 'course_id', $schedule->course_id )->get( [ 'name', 'id', 'institute_id', 'course_id', 'year' ] );

            $batch = Batches::find($schedule->batch_id) ?? new Batches();

            $dt = $batch->id ? $this->_schedule_batch_faculties_subjects($batch, $schedule->institute_id, $schedule->course_id):[];


            $institute = Institutes::find($schedule->institute_id) ?? new Institutes();

            $data['faculty_label'] = $institute->faculty_label();
            $data['discipline_label'] = $institute->discipline_label();
            $data['faculties'] = $dt['faculties'] ?? [];
            $data['disciplines'] = $dt['subjects'] ?? [];
            $data['is_combined'] = $dt['is_combined'] ?? false;
            $data['hasDisciplineFacultyChange'] = $dt['hasChange'];
            $data['topics_with_lectures'] = $this->_lectures_exams([
                'batch_id' => $batch->id,
                'institute_id' => $institute->id,
                'year' => $schedule->year,
                'course_id' => $schedule->course_id,
                'session_id' => $schedule->session_id,
                'faculty_id' => $schedule->faculty_id,
                'subject_id' => $schedule->subject_id,
                'bcps_subject_id' => $schedule->bcps_subject_id,
            ]);
        }


        return $data;

    }

    protected function _batches($input)
    {

        return Batches::where([
            'year' => $input['year'] ?? '',
            'institute_id' => $input['institute_id'] ?? '',
            'course_id' => $input['course_id'] ?? '',
        ])->get(['name', 'id', 'institute_id', 'course_id', 'year']);
    }

    public function course_data(Request $request)
    {

        $course = Courses::find($request->course_id);
        $data = [];
        $data['batches'] = $this->_batches($request->all('year', 'institute_id', 'course_id'));

        return $data;
    }

    function removeDeletingItem(Collection &$collection, $value)
    {
        $index = $collection->search($value);
        $collection = $collection->forget($collection->search($index === false ? -1 : $index));
    }

    function save_subject_faculty_relation($schedule_id, Request $request)
    {
        $institute = Institutes::find($request->institute_id);

        if ($institute->type == 1) {
            $this->save_relation(
                BatchesSchedulesFaculties::class,
                ['schedule_id' => $schedule_id],
                $request->faculty_id,
                ['schedule_id' => $schedule_id, 'faculty_id' => '@value@']
            );
        }

        if ($institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {
            $this->save_relation(
                BatchesSchedulesSubjects::class,
                ['schedule_id' => $schedule_id],
                $request->subject_id,
                ['schedule_id' => $schedule_id, 'subject_id' => '@value@']
            );
        }
    }

    private function _schedule_batch_faculties_subjects( Batches  $batch, $institute_id, $course_id )
    {
        $institute = Institutes::where('id', $institute_id)->first();

        if ($batch->fee_type == "Discipline_Or_Faculty" || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {

            $faculties = null;

            if ($institute->type ==  1) {
                $faculties = Faculty::with('subjects')->where(['institute_id' => $institute_id, 'course_id' => $course_id])->get(['name', 'id']);
            }

            $subjects = null;

            if ($institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {

                $subjects = Subjects::where(['institute_id' => $institute_id, 'course_id' => $course_id])->get(['name', 'id']);

                if ($institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {

                    $course = Courses::find($course_id);
                    $faculties = $course->combined_faculties()->get(['name', 'id']);
                    $subjects = $course->combined_disciplines()->get(['name', 'id']);
                }
            }

            return [
                'subjects' => $subjects,
                'faculties' => $faculties,
                'hasChange' => true,
                'is_combined' =>  $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID
            ];
        }

        return [
            'subjects' => null,
            'faculties' => null,
            'hasChange' => false,
            'is_combined' =>  $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID
        ];
    }

    public function faculties_disciplines(Request $request)
    {
        $institute_id = $request->institute_id;
        $course_id = $request->course_id;
        $batch_id = $request->batch_id;
        $institute = Institutes::where('id', $institute_id)->first();
        $is_combined = AppServiceProvider::$COMBINED_INSTITUTE_ID == $institute->id;

        $batch = Batches::where('id', $batch_id)->first();

        if ($batch && $batch instanceof Batches) {

            //dd( $request->all() );

            $data = $this->_schedule_batch_faculties_subjects($batch, $institute_id, $course_id);

            return response([
                'faculty_label' => $institute->faculty_label(),
                'discipline_label' => $institute->discipline_label(),
                'faculties' => $data['faculties'] ?? [],
                'disciplines'  => $data['subjects'] ?? [],
                'hasDisciplineFacultyChange'  => $data['hasChange'],
                'is_combined'  => $is_combined,
            ]);
        }

        return  response([
            'faculty_label' => 'Faculty',
            'discipline_label' => 'Discipline',
            'faculties' => [],
            'disciplines'  => [],
            'hasDisciplineFacultyChange' => false,
            'is_combined'  => $is_combined,
        ]);
    }

    public function save_fb_links(BatchesSchedules $batch_schedule, Request $request)
    {

        if (is_array($request->fb_links)) {
            $meta = $batch_schedule->meta()->where('key', 'fb_links')->first();

            if ($meta == null) {
                $meta = new BatchesSchedulesMeta();
                $meta->schedule_id = $batch_schedule->id;
            }

            $meta->key = 'fb_links';
            $meta->value = json_encode($request->fb_links);
            $meta->save();

            return ['data' => $meta];
        }
    }

}
