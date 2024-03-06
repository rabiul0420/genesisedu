@php
    $courses->prepend( '--Select Course--', '' );
@endphp


@if( ( $selection_only ?? false ) === true )
    {!! Form::select( $name, $courses, $selected_course_id, ['class'=>'form-control','required'=>'required','id' => $id]) !!}
@else
    <div class="form-group">
        <label class="col-md-{{$label_column_count ?? '3' }} control-label">{{ $label ?? 'Course'}} (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-{{$column_count ?? '3' }}">
            <div class="input-icon right">
                {!! Form::select( ( $name ?? 'course_id' ), $courses, $selected_course_id, ['class'=>'form-control','required'=>'required','id' => ($id ?? 'course_id')]) !!}
            </div>
        </div>
    </div>
@endif