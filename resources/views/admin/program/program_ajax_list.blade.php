@can('Program')
<a href="{{ url('admin/program/'.$program_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Program')
<a href="{{ url('admin/program-content/'.$program_list->id) }}" class="btn btn-xs btn-info">Contents</a>
@endcan
@can('Program')
{!! Form::open(array('route' => array('program.destroy', $program_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan