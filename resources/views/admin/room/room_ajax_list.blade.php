@can('Room')
<a href="{{ url('admin/room/'.$room_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Room')
<a href="{{ url('admin/room-slot-list/'.$room_list->id) }}" class="btn btn-xs btn-info">Slots</a>
@endcan
@can('Room')
{!! Form::open(array('route' => array('room.destroy', $room_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan