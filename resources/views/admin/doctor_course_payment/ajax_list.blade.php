@can('Doctor Edit')
    <a href="{{ url('admin/doctors/'.$doctors_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Doctor Show')
    <a href="{{ url('admin/doctors/'.$doctors_list->id) }}" class="btn btn-xs btn-primary">View</a>
@endcan
@can('Doctor Delete')
    {!! Form::open(array('route' => array('doctors.destroy', $doctors_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
@endcan