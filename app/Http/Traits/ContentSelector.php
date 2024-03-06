<?php


namespace App\Http\Traits;


use App\Batches;
use App\Courses;
use App\CourseSessions;
use App\Faculty;
use App\Institutes;
use App\Providers\AppServiceProvider;
use App\Subjects;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Sessions;

trait ContentSelector
{

    protected $content_data = [];

    protected static function institutes_view( ){
        return 'layouts.contents.institutes';
    }

    protected static function sessions_view( ){
        return 'layouts.contents.sessions';
    }

    protected static function courses_view( ){
        return 'layouts.contents.courses';
    }

    protected static  function batches_view( ){
        return 'layouts.contents.batches';
    }

    protected static  function faculties_view( ){
        return 'layouts.contents.faculties';
    }

    protected static  function residency_disciplines_view( ){
        return 'layouts.contents.residency-disciplines';
    }

    protected static  function bcps_disciplines_view( ){
        return 'layouts.contents.bcps-disciplines';
    }

    protected function set_content_data( ){

    }

    protected $_selection_config = [];

    protected function selection_config( ){
        return [ ];
    }

    private function get_selection_config( $content_name, $key, $default = '' ){
        return $this->_selection_config[$content_name][$key] ?? $default;
    }

    protected function set_config( $content_name, &$data = []){
        $this->_selection_config = $this->selection_config( );
        $data[ 'selection_only' ] = $this->get_selection_config( $content_name, 'selection_only', false );
        $data[ 'label' ] = $this->get_selection_config( $content_name, 'label', null );
        $data[ 'label_column_count' ] = $this->get_selection_config( $content_name, 'label_column_count', 3 );
        $data[ 'column_count' ] = $this->get_selection_config( $content_name, 'column_count', 3 );
    }

    protected function years( ){
        $year = date('Y');
        $items[$year-1] = $year-1;
        $items[$year] = $year;
        $items[$year+1] = $year+1;
        return $items;
    }

    public function institutes( Request $request, $selected = null ){
        $this->set_config('institutes', $data );
        $data[ 'name' ] = 'institute_id';
        $data[ 'id' ] = 'institute_id';
        $data[ 'selected_institute_id' ] = old( $data[ 'name' ], $request->selected ?? $selected );;
        $data[ 'institutes' ] = Institutes::active( )->pluck( 'name', 'id' );

        return view(self::institutes_view( ), $data );
    }

    public function sessions( Request $request, $selected = null, $course_id = null, $year = null ){
        $this->set_config('sessions', $data );
        $data[ 'name' ] = 'session_id';
        $data[ 'id' ] = 'session_id';
        $data[ 'sessions' ] = Collection::make([] );
        $data[ 'selected_session_id' ] = old( $data[ 'name' ], $request->selected ?? $selected );

        $course_id = $course_id ?? $request->course_id;
        $year = $year ?? $request->year;

        if( $course_id ) {
            // $data['sessions'] = CourseSessions::where( 'course_id', $course_id )
            //     ->where( 'sessions.status', 1 )  
            //     ->join( 'sessions', 'sessions.id', 'course_session.session_id' )
            //     ->pluck( 'sessions.name', 'sessions.id' );

            $data['sessions'] = Sessions::
                        join('course_year_session','course_year_session.session_id','sessions.id')
                        ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
                        ->where('course_year.status',1)
                        ->whereNull('course_year.deleted_at')
                        ->whereNull('course_year_session.deleted_at')
                        ->where( 'course_year.year',$year )
                        ->where('course_id',$course_id)
                        // ->where('show_admission_form','yes')
                        ->pluck( 'sessions.name',  'sessions.id');
        }
     
        return view(self::sessions_view(), $data );
    }

    public function courses( Request $request, $selected = null, $institute_id = null ){

        $institute_id = $institute_id ?? $request->institute_id;
        $this->set_config('courses', $data );

        $data[ 'name' ] = 'course_id';
        $data[ 'id' ] = 'course_id';
        $data[ 'selected_course_id' ] = old( $data[ 'name' ], $request->selected ?? $selected );

        $data[ 'courses' ] = Collection::make([]);

        if( $institute_id ) {
            $data[ 'courses' ] = Courses::active( )
                ->where( 'institute_id', $institute_id )->pluck( 'name', 'id' );
        }


        return view(self::courses_view(), $data );
    }

    protected function batchIsFetchable(Request $request, $selected = []){
        return true;
    }


    protected function  batches_where( Request $request, $default ){
        $where = [ ];
        $where['status'] = 1;
        $where['year'] = $default['year'] ?? $request->year;
        $where['institute_id'] = $default['institute_id'] ?? $request->institute_id;
        $where['course_id'] = $default['course_id'] ?? $request->course_id;
        $where['session_id'] = $default['session_id'] ?? $request->session_id;
        return $where;
    }

    protected function  faculties_where( Request $request, $default ){
        $where = [ ];
        $where['status'] = 1;
        $where['institute_id'] = $default['institute_id'] ?? $request->institute_id;
        $where['course_id'] = $default['course_id'] ?? $request->course_id;
        return $where;
    }

    protected function batches_query( Request $request, $builder, $selected = [] ){

    }

    protected function faculties_query( Request $request, $builder, $selected = [] ){

    }

    protected function residency_discipline_query( Request $request, $builder, $selected = [] ){

    }
    protected function bcps_discipline_query( Request $request, $builder, $selected = [] ){

    }

    public function batches( Request $request, $selected = null, $where = [] ){


        $where['year'] = $where['year'] ?? $request->year;
        $where['institute_id'] = $where['institute_id'] ?? $request->institute_id;
        $where['course_id'] = $where['course_id'] ?? $request->course_id;
        $where['session_id'] = $where['session_id'] ?? $request->session_id;


        $this->set_config('batches', $data );

        $data[ 'name' ] = 'batch_id';
        $data[ 'id' ] = 'batch_id';
        $data[ 'selected_batch_id' ] = old( $data[ 'name' ], $request->selected ?? $selected );

        $data[ 'batches' ] = Collection::make([] );
        $batches_where = $this->batches_where($request, $where );


        if( isset($batches_where['year'] )
            && isset($batches_where['institute_id'])
            && isset($batches_where['course_id'])
            && isset($batches_where['session_id'])
        ) {
            $batches = Batches::query( );
            $batches->where($batches_where );
            $this->batches_query( $request, $batches, $selected );
            $data[ 'batches' ] = $batches->pluck( 'name', 'id' );
        }

        return view(self::batches_view( ), $data );
    }


    protected function isCombined( $institute_id ){
        return $institute_id == AppServiceProvider::$COMBINED_INSTITUTE_ID;
    }


    public function residency_disciplines( Request $request, $selected = null, $where = [] ){
        $where['institute_id'] = $where['institute_id'] ?? $request->institute_id;
        $where['faculty_id'] = $where['faculty_id'] ?? $request->course_id;

        $isCombined = $this->isCombined( $where['institute_id'] );
        $this->set_config(__FUNCTION__, $data );

        $data[ 'label' ] = ($isCombined ? 'Residency ':'').$data['label'];
        $data[ 'name' ] = 'subject_id';
        $data[ 'id' ] = 'subject_id';
        $data[ 'selected_bcps_discipline_id' ] = old( $data[ 'name' ], $request->selected ?? $selected );
        $where[ 'status' ] = 1;
        $data[ 'bcps_disciplinees' ] = Collection::make([] );

        if(
            isset( $where['institute_id'] )
            && isset( $where['faculty_id'] )
        ) {
            $disciplines = Subjects::query( );
            $disciplines->where($where );
            $this->bcps_discipline_query( $request, $disciplines, $selected );
            $data[ 'bcps_disciplinees' ] = $disciplines->pluck( 'name', 'id' );
        }

        return view(self::bcps_disciplines_view( ), $data );
    }

    public function bcps_disciplines( Request $request, $selected = null, $where = [] ){
        $where['institute_id'] = $where['institute_id'] ?? $request->institute_id;
        $where['course_id'] = $where['course_id'] ?? $request->course_id;
        $batch_id = $where['batch_id'] ?? $request->batch_id;

        if( isset($where['batch_id']) )
            unset( $where['batch_id']);

        $faculty_discipline_visibility = $this->faculty_discipline_visibility( $batch_id,$where['institute_id'] );

        $data[ 'visibility' ] = $faculty_discipline_visibility['bcps_discipline'] ?? false;

        $isCombined = $this->isCombined( $where['institute_id'] );
        $this->set_config(__FUNCTION__, $data );

        $data[ 'label' ] = ($isCombined ? 'BCPS ':'') . ($data['label'] ?? 'Discipline');
        $data[ 'name' ] = ($isCombined ? 'bcps_subject_id':'subject_id');
        $data[ 'id' ] = 'bcps_subject_id';
        $data[ 'selected_bcps_discipline_id' ] = old( $data[ 'name' ], $request->selected ?? $selected );
        $data[ 'bcps_disciplinees' ] = Collection::make([] );

        $where[ 'status' ] = 1;
        if(
            isset( $where['institute_id'] )
            && isset( $where['course_id'] )
            && $data[ 'visibility' ]
        ) {
            if( $isCombined ) {
                $data[ 'bcps_disciplinees' ] = (new Courses())->combined_disciplines( )->pluck( 'name', 'id' );
            } else {
                $disciplines = Subjects::query( );
                $disciplines->where($where );
                $this->bcps_discipline_query( $request, $disciplines, $selected );
                $data[ 'bcps_disciplinees' ] = $disciplines->pluck( 'name', 'id' );
            }
        }

        return view(self::bcps_disciplines_view( ), $data );
    }

    protected static $facultyDisciplineVisibility = null;

    private function faculty_discipline_visibility( $batch_id, $institute_id ){

        $changed = false;
        if( self::$facultyDisciplineVisibility === null || (
            ( self::$facultyDisciplineVisibility['batch_id'] ?? null ) != $batch_id ||
            ( self::$facultyDisciplineVisibility['institute_id'] ?? null ) != $institute_id
        ) ) {

            $changed = true;
            $institute = Institutes::where('id', $institute_id)->first();
            $batch = Batches::where('id', $batch_id)->first();
        }

        if( $changed ) {
            $faculty = false;
            $residency_discipline = false;
            $bcps_discipline = false;

            if ( ( $batch->fee_type ?? '') == "Discipline_Or_Faculty" || ($institute->id ?? '') == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

                if ($institute->type ==  1) {
                    $faculty = true;
                    $bcps_discipline = false;
                    $residency_discipline = true;
                }else if ( $institute->type == 0 ) {
                    $faculty = false;
                    $bcps_discipline = true;
                    $residency_discipline = false;
                }

                if( $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {
                    $residency_discipline = true;
                    $bcps_discipline = true;
                    $faculty = true;
                }
            }

            self::$facultyDisciplineVisibility = [
                'batch_id' => $batch_id,
                'institute_id' => $institute_id,
                'faculty' => $faculty,
                'residency_discipline' => $residency_discipline,
                'bcps_discipline' => $bcps_discipline,
            ];
        }

        return self::$facultyDisciplineVisibility;

    }

    public function faculties( Request $request, $selected = null, $where = [] ){

        $where['institute_id'] = $where['institute_id'] ?? $request->institute_id;
        $where['course_id'] = $where['course_id'] ?? $request->course_id;
        $batch_id = $where['batch_id'] ?? $request->batch_id;
        $faculty_discipline_visibility = $this->faculty_discipline_visibility( $batch_id,$where['institute_id'] );

        $this->set_config(__FUNCTION__, $data );
        $data[ 'visibility' ] = $faculty_discipline_visibility['faculty'] ?? false;
        $data[ 'name' ] = 'faculty_id';
        $data[ 'id' ] = 'faculty_id';
        $data[ 'selected_faculty_id' ] = old( $data[ 'name' ], $request->selected ?? $selected );

        $data[ 'faculties' ] = Collection::make([] );
        $batches_where = $this->faculties_where($request, $where );


        if( isset( $batches_where['institute_id'] ) && isset( $batches_where['course_id'] ) && $data[ 'visibility' ] ) {

            if( $batches_where['institute_id'] == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {
                $data[ 'faculties' ] = ( new Courses( ) )->combined_faculties()->pluck( 'name', 'id' );
            }else {
                $data[ 'faculties' ] = Faculty::query( );
                $data[ 'faculties' ]->where($batches_where );
                $this->faculties_query( $request, $data[ 'faculties' ], $selected );
                $data[ 'faculties' ] = $data[ 'faculties' ]->pluck( 'name', 'id' );
            }

        }

        return view(self::faculties_view( ), $data );
    }


    protected function faculties_subjects( Batches $batch, $institute_id, $course_id )
    {
        $institute = Institutes::where('id', $institute_id)->first();

        if ($batch->fee_type == "Discipline_Or_Faculty" || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID) {

            $faculties = null;

            if ($institute->type ==  1) {
                $faculties = Faculty::with('subjects')
                    ->where([ 'institute_id' => $institute_id, 'course_id' => $course_id ])->get(['name', 'id']);
            }

            $subjects = null;

            if ($institute->type == 0 || $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

                $subjects = Subjects::where(['institute_id' => $institute_id, 'course_id' => $course_id])->get(['name', 'id']);

                if ( $institute->id == AppServiceProvider::$COMBINED_INSTITUTE_ID ) {

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


}
