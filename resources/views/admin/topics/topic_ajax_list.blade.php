@can('Topic')
<a href="{{ url('admin/topics/'.$topic_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Topic')
<a href="{{ url('admin/topics-contents/'.$topic_list->id) }}" class="btn btn-xs btn-info">Contents</a>
@endcan
@can('Topic')
{!! Form::open(array('route' => array('topics.destroy', $topic_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan