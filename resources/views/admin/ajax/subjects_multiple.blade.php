@if( $subjects )
    <div class="form-group">
        <label class="col-md-3 control-label">{{$discipline_label ?? 'Discipline'}}</label>
        <div class="col-md-3">
            @php  request()->get( 'prepend' ) != 'false' ?  $subjects->prepend('Select Discipline tttttttt', '') : null; @endphp
            {!! Form::select('subject_id[]',$subjects, old('subject_id')?old('subject_id'):'',['class'=>'form-control  select2 ',
                'data-placeholder' => 'Select Discipline',
                'multiple' => 'multiple','id'=>'subject_id']) !!}<i></i>
        </div>

        <input type="checkbox" id="checkbox" > Select All
    </div>
@endif
