<div class="form-group row">
    <label for="doc_info" class="col-sm-2 col-form-label mt-3">Upazila :</label>
    <div class="col-sm-5 mt-3">
        @php  $upazilas->prepend('Select Upazila', ''); @endphp
        {!! Form::select('present_upazila_id',$upazilas, old('present_upazila_id'),['class'=>'form-control']) !!}<i></i>
    </div>
</div>


