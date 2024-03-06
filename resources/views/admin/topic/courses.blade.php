@php
    $institute_id = request( )->institute_id ?? ( $institute_id ?? null );

    $name = $name ?? 'course_id';
    $id = $id ?? 'course_id';
    $selected_course_id =   request( )->selected_course_id ?? ( old( $name, $selected_course_id ?? '' ) );


    if( $institute_id ) {
        $courses = \App\Courses::active()->where( 'institute_id', $institute_id )->pluck( 'name', 'id' );
    } else {
        $courses = \Illuminate\Support\Collection::make([]);
    }

    $courses->prepend( '--Select Course--', '');
@endphp

<div class="form-group">
    <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">
            {!! Form::select( $name, $courses, $selected_course_id, ['class'=>'form-control','required'=>'required','id' => $id]) !!}<i></i>
        </div>
    </div>
</div>