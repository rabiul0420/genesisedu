@php

    use App\Subjects;


    $faculty_ids = $faculty_ids ?? request()->faculty_ids;
    $has_input = (is_array( $faculty_ids ) && count( $faculty_ids )
        || $faculty_ids instanceof \Illuminate\Support\Collection && $faculty_ids->count() );

    $institute_id = $institute_id ?? request()->institute_id;
    $course_id = $course_id ?? request()->course_id;
    $selected_subject_ids = $selected_subject_ids ?? request()->selected_subject_ids;
    $combined_institute_id = \App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID;


    $is_combined =  $institute_id == $combined_institute_id;
    $label = ( $is_combined ? " Residency ":""  ) . ' Disciplines';

    if( $is_combined ) {
        $institute_id = $is_combined ? \App\Providers\AppServiceProvider::$BSMMU_INSTITUTE_ID: $institute_id;
        $course_id = $is_combined ? \App\Providers\AppServiceProvider::$MPH_DIPLOMA_COURSE_ID : $institute_id;
    }


    //dd( $faculty_ids );

    $subjects = Subjects::where( 'institute_id', $institute_id )
        ->where( 'course_id', $course_id )
        ->where('status' , 1)
        ->whereIn( 'faculty_id', is_array( $faculty_ids ) || $faculty_ids instanceof \Illuminate\Support\Collection ? $faculty_ids:[] )
        ->pluck( 'name',  'id' );


    //$subjects = \Illuminate\Support\Collection::make([]);


@endphp
@if( $has_input )
    <div class="form-group">
        <label class="col-md-3 control-label">{{ $label }}</label>
        <div class="col-md-3">
            {!! Form::select(
                'subject_ids[]',
                $subjects,
                ( $selected_subject_ids ?? '' ),
                [
                    'class'         => 'form-control select2 faculty-subject-selection',
                    'id'            => 'subject_id',
                    'required'      => 'required',
                    'multiple'      => 'multiple',
                    'data-placeholder'   => 'Select ' .$label,
                ]
            ) !!}<i></i>
        </div>

        <input type="checkbox" data-target="#subject_id" class="all-selector" > Select All

    </div>
@endif