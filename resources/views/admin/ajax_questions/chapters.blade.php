<div class="form-group">
    <label class="col-md-3 control-label">Chapter (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
    <div class="col-md-4">
        @php  $chapters->prepend('Select Chapter', ''); @endphp
        {!! Form::select('chapter_id',$chapters, '',['class'=>'form-control','required'=>'required','id'=>'chapter_id']) !!}<i></i>
    </div>      
</div>


