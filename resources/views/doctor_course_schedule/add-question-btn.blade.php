<form action="{{ url('/question-submit' ) }}" method="POST" >
    <input type="hidden" name="_token" value="{{csrf_token()}}" >
    <input type="hidden" name="doctor_course_id" value="{{$doctor_course_id ?? 0}}" >
    <input type="hidden" name="lecture_video_id" value="{{ $class_id }}" >
    <button type="submit" class="btn btn-sm py-2 box-2"  {{ ( $disabled ?? false ) ? 'disabled':'' }}>
        Ask Question
    </button>
</form>
