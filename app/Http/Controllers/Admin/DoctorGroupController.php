<?php

namespace App\Http\Controllers\Admin;

use App\Batches;
use App\Courses;
use App\DoctorGroup;
use App\DoctorGroupSelectedBatchId;
use App\DoctorsCourses;
use App\DoctorSpecialBatch;
use App\Exam;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Group;
use App\Result;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DoctorGroupController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['doctor_special_batches'] = DoctorSpecialBatch::select('doctor_special_batches.*',
        DB::raw('(select count(id) from doctors_courses where course_id=doctor_special_batches.course_id and batch_id=doctor_special_batches.batch_id) as total_doctor'))->get();
        

        return view('admin.doctor_group.index',$data);
    }

    // public function doctor_group_list()
    // {
    //     $doctor_group_list = DB::table('doctor_groups as d1')
    //                         ->join( 'doctors as d2', 'd1.doctor_id' , '=' , 'd2.id' )
    //                         ->join( 'faculties as d3' , 'd1.faculty_id' , '=' , 'd3.id' )
    //                         ->join( 'subjects as d4' , 'd1.discipline_id' , '=' , 'd4.id' )
    //                         ->join( 'group as d5' , 'd1.group_id' , '=' , 'd5.id' )
    //                         ->join( 'batches as d6' , 'd1.batch_id' , '=' , 'd6.id' )
    //                         ;
                            


    //     $doctor_group_list = $doctor_group_list->select( 'd1.*', 'd2.name as doctor_name', 'd2.bmdc_no as doctor_bmdc_no', 'd2.main_password as doctor_main_password',
    //                 'd3.name as faculty_name','d4.name as discipline_name' , 'd5.name as group_name' );


    //     return DataTables::of($doctor_group_list)
    //             ->addColumn('action', function($doctor_group_list){
    //                 return view('admin.doctor_group.ajax_list', compact('doctor_group_list'));
    //             })
    //             ->make(true);
    // }

    public function create( )
    {

        //$this->update_batch_ids(10, [ 211,232,245,251,254,270,271,275,283,284,297,298,301,306,307,310,252 ]);

        $data[ 'courses' ] = Courses::where('status', 1)->get()->pluck('name', 'id');
        $data[ 'years' ]=([ '' => 'Select Year' ]);
        $data[ 'title' ] = 'Doctor Group Search';

        for( $year = date('Y')+1; $year >= 2017; $year-- ){
            $data['years'][$year]=$year;
        }

        return view( 'admin.doctor_group.create', $data );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( Request $request )
    {

        $validation = Validator::make( $request->all( ), [
            'batch_id' => 'required|array',
            'mark' => 'required',
         
            'minimum_exam_attened' => 'required',
            // dd($request),
            'special_batch_id' => 'required|unique:doctor_special_batches,batch_id',
        ]);

        if( $validation->fails( ) ) {
            Session::flash( 'class', 'alert-danger' );
            Session::flash( 'message', __('Please Input proper data!' ) );
            return back()->withInput( );
        }

        $existed_doctor_ids = DoctorsCourses::where( 'batch_id', $request->special_batch_id )->select( 'doctor_id' );


        $data = Result::select(  'doctors_courses.*', 'session_code' )
        ->join( 'doctors_courses', 'doctors_courses.id', '=', 'results.doctor_course_id' )
        ->join( 'sessions', 'doctors_courses.session_id', '=', 'sessions.id' )
        ->whereIn( DB::raw(' ( SELECT `batch_id`	FROM doctors_courses WHERE id = doctor_course_id)' ), $request->batch_id  )
        ->whereNotIn( 'doctor_id', $existed_doctor_ids )
        ->groupBy( 'doctor_course_id' )
        ->having( DB::raw( ' COUNT( doctor_course_id )' ), '>=', $request->minimum_exam_attened  )
        ->having( DB::raw( ' AVG( obtained_mark_percent )' ), '>=', $request->mark );

        $doctorGroupData = $data->get( );

        $total = $doctorGroupData->count( );

        if( $total ) {

            foreach ( $doctorGroupData as $doctor_course ) {

                $reg = $this->next_reg_no( $doctor_course )->first();

                unset( $doctor_course->id );

                $year = substr( $doctor_course->year, -2, 2 );

                $doctor_course->reg_no_first_part = $year.$doctor_course->session_code;
                $doctor_course->reg_no_last_part = $reg ? $reg->last_part:str_pad(1,5,"0",STR_PAD_LEFT );
                $doctor_course->reg_no_last_part_int = $reg ? $reg->last_part_int:1;

                $doctor_course->reg_no = $doctor_course->reg_no_first_part.$doctor_course->reg_no_last_part;

                $doctor_course->created_at = Carbon::now();
                $doctor_course->updated_at = Carbon::now();

                $doctor_course->batch_id = $request->special_batch_id;
                $doctor_course->updated_by = Auth::id();
                $doctor_course->payment_completed_by_id = Auth::id();

                unset( $doctor_course->session_code );

                DoctorsCourses::insert( $doctor_course->toArray( ) );
            }

            $data =   [
                'year' => $request->year,
                'course_id' => $request->course_id,
                'batch_id' => $request->special_batch_id,
                'average_obtained_mark_percent' => $request->mark,
                'minimum_exam_attentded' => $request->minimum_exam_attened
            ];

            $doctorSpecialBatch = DoctorSpecialBatch::where( $data );

            Session::flash( 'class', 'alert-success' );
            Session::flash( 'message', __( $total . ' doctor(s) inserted on special group' ) );
            
            if( !$doctorSpecialBatch->exists( ) ) {
                $data[ 'count' ] = $total;
                $group_id = DoctorSpecialBatch::insertGetId( $data );

                $this->update_batch_ids( $group_id, $request->batch_id );

            } else {
                
                $specialBatch = $doctorSpecialBatch->first( );
                $specialBatch->count += $total;
                $specialBatch->save();

                $this->update_batch_ids( $specialBatch->id, $request->batch_id );
            }
            
        } else {
            Session::flash( 'class', 'alert-success' );
            Session::flash( 'message', __('No doctor matched in selected criteria' ) );
            return back()->withInput();
        }
        
        return redirect( )->route( 'doctor-group.index' );
    }


    protected function next_reg_no( Result $doctorCourse  ){
        $year = substr( $doctorCourse->year, -2, 2 );

        return DoctorsCourses::select(
            DB::raw('LPAD( MAX( reg_no_last_part_int ) + 1, 5,0 ) as last_part' ),
            DB::raw('MAX( reg_no_last_part_int ) + 1  as last_part_int' )
        )
            ->where( 'is_trash', 0)
            ->where( 'reg_no_first_part', $year.$doctorCourse->session_code );

    }

    public function doctor_matched_in_group(){

    }

    public function update_batch_ids( $group_id, $ids ){
        $data = [];

        $batches = DoctorGroupSelectedBatchId::where( 'group_id', $group_id );
        $batches->update( ['deleted_by' =>  Auth::id() ]);
        $batches->delete();

        $bat = DoctorGroupSelectedBatchId::where( 'group_id', $group_id )->whereIn( 'batch_id', ( !is_array($ids) ?  []: $ids ) )->withTrashed();

        $existed = $bat->pluck( 'batch_id' )->toArray();


        $bat->update( [ 'deleted_at' => null, 'deleted_by' => null ] );


        if( is_array($ids) ) {            
            foreach ( $ids as $id ) {
                if( !in_array( $id, $existed )) {
                    $data[] = [
                        'group_id' => $group_id,
                        'batch_id' => $id,
                    ];
                }
            }
        }

        DoctorGroupSelectedBatchId::insert( $data );

    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $specialBatch = DoctorSpecialBatch::find($id);

        $data[ 'title' ] = 'Doctor Group Search';

        $data[ 'action' ] = 'edit';
        $data[ 'id' ] = $id;

        $data[ 'special_batch' ] = $specialBatch ;
        $data[ 'courses' ] = Courses::where('status', 1)->get()->pluck('name', 'id');
        $data[ 'years' ]=([ '' => 'Select Year' ]);

        $data[ 'selected_batches' ] = $specialBatch->selected_batches->pluck('batch_id') ;


        $batches = Batches::with( 'course' )
            ->where('year', $specialBatch->year )
            ->where('course_id', $specialBatch->course_id )->get( [ 'name', 'id' , 'is_special' ] );

        $data[ 'batches' ] = $batches->pluck('name','id' );
        $data[ 'special_batches' ] = $batches->where('is_special', 'Yes' )->pluck('name','id' );

        for( $year = date('Y')+1; $year >= 2017; $year-- ){
            $data['years'][$year]=$year;
        }

        return view( 'admin.doctor_group.create', $data );

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
        $validation = Validator::make( $request->all( ), [
            'batch_id' => 'required|array',
            'mark' => 'required',
            'minimum_exam_attened' => 'required',
        ]);

        if( $validation->fails( ) ) {
            Session::flash( 'class', 'alert-danger' );
            Session::flash( 'message', __('Please Input proper data!' ) );
            return back()->withInput( );
        }

        // return
        $specialBatch = DoctorSpecialBatch::find($id);

        $specialBatch->year= $request->year;
        $specialBatch->course_id= $request->course_id;
        $specialBatch->average_obtained_mark_percent= $request->mark;
        $specialBatch->minimum_exam_attentded= $request->minimum_exam_attened;


        $specialBatch->save( );
        
        // return $specialBatch;

        $data = Result::query()
            ->select([
                'doctors_courses.*',
                'session_code',
            ])
            ->join('doctors_courses', 'doctors_courses.id', '=', 'results.doctor_course_id')
            ->join('sessions', 'doctors_courses.session_id', '=', 'sessions.id')
            ->whereIn(DB::raw('(SELECT `batch_id` FROM doctors_courses WHERE id = doctor_course_id)'), $request->batch_id)
            ->groupBy('doctor_course_id')
            ->having(DB::raw('COUNT(doctor_course_id)'), '>=', $request->minimum_exam_attened)
            ->having(DB::raw('AVG(obtained_mark_percent)'), '>=', $request->mark);

        // return
        $doctor_group_data = $data->get();

        $total = $doctor_group_data->count();

        // return
        $allow_doctor_ids = $doctor_group_data->pluck('doctor_id')->toArray();

        // delete not allow doctor course
        DoctorsCourses::query()
            ->where('batch_id', $specialBatch->batch_id)
            ->whereNotIn('doctor_id', $allow_doctor_ids)
            ->update([
                'deleted_at'    => now(),
                'deleted_by'    => Auth::id(),
            ]);

        // restore allow doctor course
        DoctorsCourses::query()
            ->where('batch_id', $specialBatch->batch_id)
            ->whereNotIn('doctor_id', $allow_doctor_ids)
            ->update([
                'deleted_at'    => null,
                'updated_by'    => Auth::id(),
            ]);

        // return
        $already_doctor_ids = DoctorsCourses::query()
            ->where('batch_id', $specialBatch->batch_id)
            ->whereIn('doctor_id', $allow_doctor_ids)
            ->pluck('doctor_id')
            ->toArray();

        // return
        $new_doctor_ids = array_values(array_diff($allow_doctor_ids, $already_doctor_ids));

        // return
        $new_doctor_group_data = $doctor_group_data->whereIn('doctor_id', $new_doctor_ids);
            
        // return
        $new_total = count($new_doctor_group_data ?? []) ?? 0;

        if( $new_total ) {
            foreach ( $new_doctor_group_data as $doctor_course ) {
                $reg = $this->next_reg_no( $doctor_course )->first();

                unset( $doctor_course->id );

                $year = substr( $doctor_course->year, -2, 2 );

                $doctor_course->reg_no_first_part = $year . $doctor_course->session_code;
                $doctor_course->reg_no_last_part = $reg ? $reg->last_part : str_pad(1, 5, "0", STR_PAD_LEFT);
                $doctor_course->reg_no_last_part_int = $reg ? $reg->last_part_int : 1;

                $doctor_course->reg_no = $doctor_course->reg_no_first_part . $doctor_course->reg_no_last_part;

                $doctor_course->created_at = Carbon::now();
                $doctor_course->updated_at = Carbon::now();

                $doctor_course->batch_id = $specialBatch->batch_id;
                $doctor_course->updated_by = Auth::id();
                $doctor_course->payment_completed_by_id = Auth::id();

                unset( $doctor_course->session_code );

                DoctorsCourses::insert( $doctor_course->toArray( ) );
            }
        }

        
        $specialBatch->count = $total;
        $specialBatch->save();

        $this->update_batch_ids( $specialBatch->id, $request->batch_id );

        Session::flash( 'class', 'alert-success' );
        Session::flash( 'message', __('Data updated successfully!' ) );
        return redirect( )->route( 'doctor-group.index' );
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
