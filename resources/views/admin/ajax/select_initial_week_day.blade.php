<label class="col-md-1 control-label">Select Batch Days (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
<div class="col-md-3">
    <div class="input-icon right">
        @php  $week_days->prepend('Select Week Days', ''); @endphp
        {!! Form::select('wd_ids[]',$week_days, $week_day ,['class'=>'form-control select2 ', 'multiple' => 'multiple','required'=>'required']) !!}<i></i>
    </div>
</div>






