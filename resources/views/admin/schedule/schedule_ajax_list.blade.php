@can('Schedule')
<a href="{{ url('admin/schedule/'.$schedule_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Schedule')
<a href="{{ url('admin/schedule-content/'.$schedule_list->id) }}" class="btn btn-xs btn-info">Contents</a>
@endcan
@can('Schedule')
{!! Form::open(array('route' => array('schedule.destroy', $schedule_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan