@can('Doctors Course Edit')
<a href="{{ url('admin/doctors-courses/'.$doctors_courses_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Doctors Course Delete')
{!! Form::open(array('route' => array('doctors-courses.destroy', $doctors_courses_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan