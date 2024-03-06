
<a href="{{ url('admin/upload-result/'.$exam_list->id) }}" class="btn btn-xs btn-primary">Upload Result</a>
<a href="{{ url('admin/view-result/'.$exam_list->id) }}" class="btn btn-xs btn-primary">View Result</a>
@can('Exam')
    @if( $exam_list->doctor_exams->count() == 0  )
        <a href="{{ url('admin/exam/'.$exam_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
    @endif

    <a href="{{ url('admin/exam/'.$exam_list->id.'/duplicate') }}" class="btn btn-xs btn-primary">Duplicate</a>
@endcan

@if ($user->hasRole('Administrator'))
    {!! Form::open(array('route' => array('exam.destroy', $exam_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
@endif


@if ($exam_list->sif_only=='No')
    <a href="{{ url('admin/exam-questions/'.$exam_list->id) }}" class="btn btn-xs btn-info">Exam questions</a>
    <a  target="_blank" href="{{ url('admin/exam-print/'.$exam_list->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-print"></i> View</a>
    <a  target="_blank" href="{{ url('admin/exam-print-ans/'.$exam_list->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-print"></i> View+Ans</a>
    <a  target="_blank" href="{{ url('admin/exam-print-onlyans/'.$exam_list->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-print"></i> Only Ans</a>
@endif