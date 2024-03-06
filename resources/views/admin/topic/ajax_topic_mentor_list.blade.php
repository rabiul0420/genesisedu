@if(is_array($class_chapter_list->teacher_name))
@foreach($class_chapter_list->teacher_name as $teacher)
<span>{{ $teacher }}</span><br>
@endforeach
@endif


