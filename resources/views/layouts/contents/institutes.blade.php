@php
    $institutes->prepend( '--Select Institute--', '');
@endphp

@if( ( $selection_only ?? false ) === true )
    {!! Form::select( 'institute_id', $institutes, $selected_institute_id, [ 'class'=>'form-control','required'=>'required','id' => $id ]) !!}
@else
    <div class="form-group">
        <label class="col-md-{{$label_column_count ?? '3' }} control-label">{{ $label ?? 'Institute'}} (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-{{$column_count ?? '3' }}">
            <div class="input-icon right">
                {!! Form::select( ($name ?? 'institute_id'), $institutes, $selected_institute_id, [ 'class'=>'form-control','required'=>'required','id' => ($id ?? 'institute_id') ]) !!}
            </div>
        </div>
    </div>
@endif
