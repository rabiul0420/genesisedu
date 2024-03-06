<a href="{{ url('admin/lecture-sheet-batch/'.$notice_batch_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
{!! Form::open(array('route' => array('lecture-sheet-batch.destroy', $notice_batch_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}