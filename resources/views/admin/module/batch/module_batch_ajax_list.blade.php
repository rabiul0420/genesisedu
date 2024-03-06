@can('Module Content')
<a href="{{ url('admin/module-batch-edit/'.$module_batch_list->module_content_id) }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Module Content')
{!! Form::open(['url' => url('admin/module-batch-delete/'.$module_batch_list->module_content_id), 'style' => 'display:inline', 'method' => 'GET']) !!}
    <button onclick="return confirm('Are you Sure to delete?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan