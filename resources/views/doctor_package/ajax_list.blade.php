<a href="{{ url('admin/lecture-sheet/'.$lecture_sheet_list->id.'/edit') }}" class="btn btn-sm btn-primary">Edit</a>
{!! Form::open(array('route' => array('lecture-sheet.destroy', $lecture_sheet_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-sm btn-danger' type="submit">Delete</button>
{!! Form::close() !!}