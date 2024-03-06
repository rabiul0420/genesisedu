
<div class="lecture_video">
    <div class="form-group col-md-2">
        <label class="control-label">Video Name</label>
        <div class="controls">
            {!! Form::select('lecture_video',$lecture_videos , '' , [ 'class'=>'form-control','required'=>'required','id'=>'video_id' ] ) !!}<i></i>
        </div>
    </div>
</div>