<div class="form-group col-md-2">
    <h5>Year <span class="text-danger"></span></h5>
    <div class="controls">
        @php  $years->prepend('Select Year', ''); @endphp
        {!! Form::select('year',$years, old('year'),['class'=>'form-control','required'=>'required']) !!}<i></i>
    </div>
</div>




