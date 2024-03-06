@can('Topic Content')
<a href="{{ url('admin/program-lecture-video-edit/'.$program_lecture_video_list->program_content_id) }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Topic Content')
{!! Form::open(['url' => url('admin/program-lecture-video-delete/'.$program_lecture_video_list->program_content_id), 'style' => 'display:inline', 'method' => 'GET']) !!}
    <button onclick="return confirm('Are you Sure to delete?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan