<?php

namespace App\Http\Controllers\Admin;

use App\Exam;
use App\ExamBatch;
use App\ExamGroup;
use App\ExamGroupExam;
use App\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ExamGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $data['exam_groups'] = ExamGroup::get( );
        $data['module_name'] = 'Exam Group';
        $data['title'] = 'Create Exam Group';
        $data['breadcrumb'] = explode('/', $_SERVER['REQUEST_URI'] );

        return view('admin.exam_group.list',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $data[ 'module_name' ] = 'Exam Batch';
        $data[ 'years' ] = $this->years();
        $data[ 'groups' ] = Group::pluck( 'name', 'id' );
        $data[ 'exams' ] = Exam::pluck( 'name', 'id' );
        $data[ 'title' ] = 'Exam Group Create';
        $data[ 'breadcrumb' ] = explode('/',$_SERVER['REQUEST_URI']);
        $data[ 'submit_value' ] = 'Submit';
        $data[ 'form_action' ] = [ 'Admin\ExamGroupController@store'];

        return view('admin.exam_group.form', $data );
    }

    protected function saveData( ExamGroup $exam_group, Request $request, $action = 'store' ){

        $exam_group->year       =   $request->year;
        $exam_group->group_id   =   $request->group_id;
        $exam_group->status     =   $request->status;

        if( $action == 'update' ) {
            $exam_group->updated_by = Auth::id();
        }else {
            $exam_group->created_by = Auth::id();
        }

        if( $exam_group->save() ) {

            $deletion = ExamGroupExam::where( 'exam_group_id', $exam_group->id )->whereNotIn( 'exam_id', $request->exam_id ?? [] );
            $deletion->update( [ 'deleted_by' => Auth::id() ] );
            $deletion->delete( );

            if( is_array( $request->exam_id ) ) {

                $allIds = ExamGroupExam::whereIn( 'exam_id' , $request->exam_id )->where( 'exam_group_id', $exam_group->id )->withTrashed( )->get( ['id', 'exam_id'] );
                $exam_ids = $allIds->pluck( 'exam_id' )->toArray();

                if( $allIds->count() ){
                    ExamGroupExam::whereIn( 'id', $allIds->pluck('id') )->onlyTrashed()->update([ 'deleted_at' => NULL, 'deleted_by' => null ]);
                }

                $insertingData = [ ];

                foreach ( $request->exam_id as $exam_id ) {

                    if( !in_array($exam_id, $exam_ids)) {
                        $insertingData[ ] = [
                            'exam_id' => $exam_id,
                            'exam_group_id' => $exam_group->id,
                        ];
                    }

                }

                ExamGroupExam::insert( $insertingData );
            }

            return redirect()->route('exam-group.index')->with([ 'message' => 'Successfully updated!' ]);
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

        $validate = Validator::make( $request->all(), [
            'group_id' => 'required'
        ]);

        if( $validate->fails( ) ) {
            return back()->with([ 'message' => 'Please input proper data!', 'class' => 'alert-danger' ]);
        }

        return $this->saveData( new ExamGroup(), $request, 'store' );
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
        $data['exam_group'] = ExamGroup::find( $id );
        $data['years'] = $this->years();
        $data['module_name'] = 'Exam Batch';
        $data['groups'] = Group::pluck( 'name', 'id' );
        $data['exams'] = Exam::pluck( 'name', 'id' );
        $data['title'] = 'Exam Group Create';
        $data['breadcrumb'] = explode('/',$_SERVER['REQUEST_URI']);
        $data['submit_value'] = 'Submit';
        $data['exam_group_exam_ids'] = $data['exam_group']->group_exams->pluck( 'exam_id' );

        $data['form_action'] = [ 'Admin\ExamGroupController@update', $id ];
        $data['form_method'] = 'PUT';

        return view('admin.exam_group.form', $data);

    }

    protected function years( ){
        $years = array( ''=> 'Select year' );
        for( $year = date("Y" ) + 1; $year >= 2017; $year-- ){
            $years[ $year ] = $year;
        }
        return $years;
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
        $this->saveData( ExamGroup::find( $id ), $request, 'update' );
        return back()->with([ 'message' => 'Successfully updated!' ]);
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
