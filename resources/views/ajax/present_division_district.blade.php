<!--<div class="form-group">-->
    <label class="col-md-1 control-label">District</label>
    <div class="col-md-3">
        @php  $districts->prepend('Select District', ''); @endphp
        {!! Form::select('present_district_id',$districts, old('present_district_id'),['class'=>'form-control']) !!}<i></i>
    </div>
<!--</div>-->