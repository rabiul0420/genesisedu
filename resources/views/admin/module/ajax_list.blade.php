<a href="{{ url('admin/module/'.$module_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
{!! Form::open(array('route' => array('module.destroy', $module_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}