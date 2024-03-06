@php
    $selection_only = request()->selection_only == 'true';
    $selected = request()->selected ?? ( request()->multiple == 'true' ? [] : '') ;

    $select_data = ['class'=>'form-control','required'=>'required','id'=>'chapter_id',  'multiple' => 'multiple', 'data-placeholder' => 'Select chapters'];

    if( request()->required == 'false' ) {
        unset( $select_data['required'] );
    }

    $data = Form::select( 'chapter_id[]',$chapters, $selected, $select_data);
@endphp

@if( $selection_only )
    {!! $data !!}
@else
    <div class="form-group">
        <label class="col-md-3 control-label">Chapter (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
        <div class="col-md-4">
            {!! $data !!}<i></i>
        </div>
    </div>
@endif


