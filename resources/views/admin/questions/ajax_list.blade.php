<a href="{{ url('admin/question/'.$mcq_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
{!! Form::open(array('route' => array('question.destroy', $mcq_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
<button type="button " class="btn btn-xs btn-primary btn_view" data-toggle="modal" id={{ $mcq_list->id }} data-target="#exampleModal">
    View
  </button>
  <button type="button" class="btn btn-primary btn-xs btn_log" data-toggle="modal" id={{ $mcq_list->id }} data-target="#exampleModal">
    Log History
  </button>