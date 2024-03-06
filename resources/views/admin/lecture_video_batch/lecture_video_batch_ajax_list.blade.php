@can('Lecture Video Batch')
    <a href="{{ url('admin/lecture-video-batch/'.$lecture_video_batch_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan

@can('Lecture Video Batch')
{!! Form::open(array('route' => array('lecture-video-batch.destroy', $lecture_video_batch_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan