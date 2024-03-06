<div class="form-group col-md-2">
    <h5>Floor <span class="text-danger"></span></h5>
    <div class="controls">
            @php $floors->prepend('Select Floor', ''); @endphp
            {!! Form::select('floor',$floors, '', ['class'=>'form-control','required'=>'required','id'=>'floor']) !!}<i></i>
        </div>
    </div>
</div>


