<div class="form-group">
    <label class="col-md-3 control-label">Lecture Video  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
    <div class="col-md-3">
            @php  $lecture_videos->prepend('Select Lecture Video', ''); @endphp
            {!! Form::select('lecture_video_id[]',$lecture_videos, '',['class'=>'form-control select2','id'=>'lecture_video_id','multiple'=>'multiple']) !!}<i></i>
    </div>
</div>