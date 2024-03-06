@can('Program Content')
<a href="{{ url('admin/program-batch-edit/'.$program_batch_list->program_content_id) }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Program Content')
{!! Form::open(['url' => url('admin/program-batch-delete/'.$program_batch_list->program_content_id), 'style' => 'display:inline', 'method' => 'GET']) !!}
    <button onclick="return confirm('Are you Sure to delete?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan