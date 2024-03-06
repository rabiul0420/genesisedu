@can('Individual View')
    <a href="{{ url('admin/users/'.$users_list->id) }}" class="btn btn-xs btn-primary">View</a>
@endcan
@can('Individual Edit')
<a href="{{ url('admin/users/'.$users_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Individual Delete')
    {!! Form::open(array('route' => array('users.destroy', $users_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
@endcan