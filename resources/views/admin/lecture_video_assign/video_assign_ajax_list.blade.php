@can('Lecture Video Assign')
    <a href="{{ url('admin/lecture-video-assign/'.$lecture_video_assign_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan

@can('Lecture Video Assign')
    {!! Form::open(array('route' => array('lecture-video-assign.destroy', $lecture_video_assign_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
@endcan