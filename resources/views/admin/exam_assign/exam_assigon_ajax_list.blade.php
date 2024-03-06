@can('Exam Assign')
    <a href="{{ url('admin/exam-assign/'.$exam_assign_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Exam Assign')
    {!! Form::open(array('route' => array('exam-assign.destroy', $exam_assign_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
@endcan