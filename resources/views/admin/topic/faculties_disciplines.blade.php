@php
    $institute_id = request( )->institute_id ?? ( $institute_id ?? null );
    $course_id = request( )->course_id ?? ( $course_id ?? null );
    $selected_faculty_ids = $selected_faculty_ids ?? '';
    $selected_subject_ids = $selected_subject_ids ?? [ ];
    $selected_bcps_subject_ids = $selected_bcps_subject_ids ?? [ ];
    $institute = \App\Institutes::where( 'id', $institute_id )->first( );

    $subjects = null;
    $faculties = null;
    $combined_institute_id = \App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID;
    $is_combined = \App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID == ($institute->id ?? '');



    if( $institute && $institute->type ==  1 ){
        $faculties = \App\Faculty::where(['institute_id'=>$institute_id,'course_id'=>$course_id])->pluck( 'name', 'id' );
    }



    if( ($institute && $institute->type == 0) || ($institute && $institute->id == $combined_institute_id ) )
    {
        if( $institute && $institute->id == \App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID ) {


            $course = \App\Courses::find( $course_id );

            if( $course instanceof \App\Courses ) {
                $faculties = $course->combined_faculties( )->pluck('name', 'id');
                $subjects = $course->combined_disciplines( )->pluck('name', 'id');
            }
        }   else {
            $subjects = \App\Subjects::where(['institute_id'=>$institute_id,'course_id'=>$course_id,'status' => '1'])->pluck('name', 'id');
        }
    }

@endphp

@if( $faculties )
    <div class="form-group">
        <label class="col-md-3 control-label">{{  $institute->faculty_label( ) }} </label>
        <div class="col-md-3">
            {!! Form::select('faculty_ids[]',$faculties, $selected_faculty_ids,
                ['class'=>'form-control  select2 ',
                    'data-placeholder' => $institute->faculty_label( ),
                    'multiple' => 'multiple',
                    'id'=>'faculty_id'])
            !!}<i></i>
        </div>
        <input type="checkbox" data-target="#faculty_id" class="all-selector" > Select All
    </div>

    <div class="faculty-subjects">
        @include( 'admin.topic.disciplines', [ 'faculty_ids' => $selected_faculty_ids, 'selected_subject_ids' => $selected_subject_ids ] )
    </div>
@endif

@if( isset( $subjects ) && $subjects !== null )
    <div class="form-group">
        <label class="col-md-3 control-label">{{  $institute->discipline_label( ) }}</label>
        <div class="col-md-3" style="max-height: 350px; overflow-y:auto; overflow-x:hidden">
            {!!
                Form::select(
                    $is_combined ? "bcps_subject_ids[]":"subject_ids[]",
                    $subjects,
                    $is_combined ? $selected_bcps_subject_ids : $selected_subject_ids,
                    [
                        'class'             => 'form-control  select2 ',
                        'data-placeholder'  => 'Select ' . $institute->discipline_label( ),
                        'multiple'          => 'multiple',
                        'id'                => $is_combined ? "bcps_subject_id":"subject_id"
                    ]
                )
            !!}
            <i></i>
        </div>
        <input type="checkbox" data-target="#{{ $is_combined ? "bcps_subject_id":"subject_id" }}" class="all-selector" > Select All
    </div>
@endif

