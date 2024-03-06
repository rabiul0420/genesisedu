<div class="form-group row">
    <label for="doc_info" class="col-sm-2 col-form-label mt-3">District :</label>
    <div class="col-sm-5 mt-3">
        @php  $districts->prepend('Select District', ''); @endphp
        {!! Form::select('present_district_id',$districts, old('present_district_id'),['class'=>'form-control']) !!}<i></i>
    </div>
</div>


