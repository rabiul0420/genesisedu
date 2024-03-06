<!--<div class="form-group">-->
    <label class="col-md-1 control-label">Upazila</label>
    <div class="col-md-3">
        @php  $upazilas->prepend('Select Upazila', ''); @endphp
        {!! Form::select('present_upazila_id',$upazilas, old('present_upazila_id'),['class'=>'form-control']) !!}<i></i>
    </div>
<!--</div>-->