<div class="institutes">
    <div class="form-group">
        <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
        <div class="col-md-3">
            <div class="input-icon right">

                @php
                $institutes = \App\Institutes::active()->pluck( 'name', 'id' );
                $institutes->prepend('Select Institute', '');
                @endphp
                {!! Form::select('institute_id',$institutes, old('institute_id')?old('institute_id'):'' ,['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>

            </div>
        </div>
    </div>
</div>

<div class="courses">

</div>

<div class="faculties">

</div>

<div class="subjects">

</div>

<div class="topics">

</div>

<div class="batches">

</div>