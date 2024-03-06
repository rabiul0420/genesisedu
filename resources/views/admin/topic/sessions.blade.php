@php
    $course_id = request( )->course_id ?? ( $course_id ?? null );
    $year = request( )->year ?? ($year ?? null);

    $name = $name ?? 'session_id';
    $id = $id ?? 'session_id';
    $selected_session_id =   request( )->selected_session_id ?? ( old( $name, $selected_session_id ?? '' ) );

    $sessions = \Illuminate\Support\Collection::make([]);

    if( $course_id || ( $action ?? 'create') == 'edit' ) {
        // $sessions = \App\CourseSessions::where( 'course_id', $course_id )
        //     ->where( 'sessions.status', 1 )
        //     ->join( 'sessions', 'sessions.id', 'course_session.session_id' )->pluck( 'sessions.name', 'sessions.id' );

    $sessions = \App\Sessions::join('course_year_session','course_year_session.session_id','sessions.id')
                ->join( 'course_year', 'course_year.id', 'course_year_session.course_year_id' )
                ->where('course_year.status',1)
                ->where('course_year.deleted_at',NULL)
                ->where('course_year.year',$year)  
                ->where('course_year_session.deleted_at',NULL)
                ->where('course_id',$course_id)
                // ->where('show_admission_form','yes')
                ->pluck('name',  'sessions.id');
    }
    $sessions->prepend('--Select--', '');
@endphp

<div class="form-group">
    <label class="col-md-3 control-label">Session (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">
            {!! Form::select( $name, $sessions, $selected_session_id, ['class'=>'form-control','required'=>'required','id' => $id]) !!}<i></i>
        </div>
    </div>
</div>
