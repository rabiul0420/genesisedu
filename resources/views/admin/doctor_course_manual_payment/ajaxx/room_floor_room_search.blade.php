<div class="form-group col-md-2">
    <h5>Room <span class="text-danger"></span></h5>
    <div class="controls">
        @php  $rooms->prepend('Select Room', ''); @endphp
        {!! Form::select('room_id',$rooms, '',['class'=>'form-control select2','required'=>'required','id'=>'room_id']) !!}<i></i>
    </div>
</div>