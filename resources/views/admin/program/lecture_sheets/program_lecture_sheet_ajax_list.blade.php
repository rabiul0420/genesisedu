@can('Topic Content')
<a href="{{ url('admin/program-lecture-sheet-edit/'.$program_lecture_sheet_list->program_content_id) }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Topic Content')
{!! Form::open(['url' => url('admin/program-lecture-sheet-delete/'.$program_lecture_sheet_list->program_content_id), 'style' => 'display:inline', 'method' => 'GET']) !!}
    <button onclick="return confirm('Are you Sure to delete?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan