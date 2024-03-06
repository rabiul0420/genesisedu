@can('Question Topic')
    <a href="{{ url('admin/question-topic/'.$question_topic_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Question Topic')
    {!! Form::open(array('route' => array('question-topic.destroy', $question_topic_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan