@can('Faculty Edit')
<a href="{{ url('admin/faculty/'.$faculty_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
 @can('Faculty Delete')
 {!! Form::open(array('route' => array('faculty.destroy', $faculty_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
 <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
 {!! Form::close() !!}
 @endcan
