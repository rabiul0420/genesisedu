@php
    $selection_only = request()->selection_only == 'true';
    $selected = request()->selected ?? ( request()->multiple == 'true' ? [] : '') ;

    $select_data = ['class'=>'form-control','required'=>'required','id'=>'topic_id',  'multiple' => 'multiple', 'data-placeholder' => 'Select topics'];

    if( request()->required == 'false' ) {
        unset( $select_data['required'] );
    }

    $data = Form::select( 'topic_id[]',$topics, $selected, $select_data);
@endphp

@if( $selection_only )
    {!! $data !!}
@else
    <div class="form-group">
        <label class="col-md-3 control-label">Topic (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
        <div class="col-md-4">
            {!! $data !!}<i></i>
        </div>
    </div>
@endif
