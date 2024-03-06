@can('Question Source')
    <a href="{{ url('admin/question-reference-exam/'.$question_reference_exam_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Question Source')
    {!! Form::open(array('route' => array('question-reference-exam.destroy', $question_reference_exam_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
@endcan