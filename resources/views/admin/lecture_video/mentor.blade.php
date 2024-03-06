<label class="col-md-3 control-label">Mentor Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
{{-- @php $topic_teachers->prepend('--Select type--', '') @endphp --}}

<div class="col-md-3">
    <select name="teacher_id" id="" class="form-control">
        <option value="">--Select type--</option>
        @foreach ($topic_teachers as $key=>$value)
            <option value="{{ $key }}">{{ $value}}</option>
        @endforeach
    </select>
    {{-- {!! Form::select( 'teacher_id', $topic_teachers->name ?? '', '', [ 'class' => 'form-control' ] ) !!}<i></i> --}}
</div>