@can('Lecture Video')
    <a href="{{ url('admin/lecture-video/'.$lecture_video_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
    <a href="{{ url('admin/lecture-video/'.$lecture_video_list->id.'/duplicate') }}" class="btn btn-xs btn-success">Duplicate</a>
    <a href="{{ url('admin/lecture-video-price/'.$lecture_video_list->id) }}" class="btn btn-xs btn-info">Price</a>
@endcan
@can('Lecture Video')
{!! Form::open(array('route' => array('lecture-video.destroy', $lecture_video_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan