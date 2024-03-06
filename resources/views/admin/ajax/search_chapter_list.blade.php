<div class="form-group col-md-3">
	<h5>Chapter <span class="text-danger"></span></h5>
	<div class="controls">
		@php  $chapter->prepend('Select Chapter', ''); @endphp
		{!! Form::select('chapter_id',$chapter, '' ,['class'=>'form-control select2','required'=>'required','id'=>'chapter_id']) !!}<i></i>
	</div>
</div>


