@can('Room')
<a href="{{ url('admin/module-schedule/'.$module_schedule_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Room')
{!! Form::open(array('route' => array('module-schedule.destroy', $module_schedule_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan