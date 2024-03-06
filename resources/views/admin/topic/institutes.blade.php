@php

    $name = $name ?? 'institute_id';
    $id = $id ?? 'institute_id';
    $selected_institute_id =   request( )->selected_institute_id ?? ( old( $name, $selected_institute_id ?? '' ) );

    $institutes = \App\Institutes::active()->pluck( 'name', 'id' );

    $institutes->prepend( '--Select Institute--', '');
@endphp

<div class="form-group">
    <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">

            {!! Form::select( $name, $institutes, $selected_institute_id, [ 'class'=>'form-control','required'=>'required','id' => $id ]) !!}<i></i>
        </div>
    </div>
</div>
