<div class="form-group col-md-2">
    <h5>Location <span class="text-danger"></span></h5>
    <div class="controls">
        @php  $locations->prepend('Select Location', ''); @endphp
        {!! Form::select('location_id',$locations, '',['class'=>'form-control select2','required'=>'required','id'=>'location_id']) !!}<i></i>
    </div>
</div>



