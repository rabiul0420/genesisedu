 @can('Discipline Edit')
    <a href="{{ url('admin/subjects/'.$discipline_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
 @endcan
 @can('Discipline Delete')
    {!! Form::open(array('route' => array('subjects.destroy', $discipline_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}   
 @endcan

