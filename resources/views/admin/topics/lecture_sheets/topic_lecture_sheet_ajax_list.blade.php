@can('Topic Content')
<a href="{{ url('admin/topic-lecture-sheet-edit/'.$topic_lecture_sheet_list->topic_content_id) }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Topic Content')
{!! Form::open(['url' => url('admin/topic-lecture-sheet-delete/'.$topic_lecture_sheet_list->topic_content_id), 'style' => 'display:inline', 'method' => 'GET']) !!}
    <button onclick="return confirm('Are you Sure to delete?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan