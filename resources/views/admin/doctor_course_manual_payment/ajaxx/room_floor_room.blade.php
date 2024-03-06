<div class="form-group">
    <label class="col-md-3 control-label">Room (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
    <div class="col-md-3">
        <div class="input-icon right">
            @php  $rooms->prepend('Select Room', ''); @endphp
            {!! Form::select('room_id',$rooms, '',['class'=>'form-control select2','required'=>'required','id'=>'room_id']) !!}<i></i>
        </div>
    </div>
</div>


