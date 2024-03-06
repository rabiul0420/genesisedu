@can('Installment Payment')
<a href="{{ url('admin/doctor-course-list/'.$doctor_course_list->id) }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Installment Payment')
{!! Form::open(['url' => url('admin/module-batch-delete/'.$doctor_course_list->id), 'style' => 'display:inline', 'method' => 'GET']) !!}
    <button onclick="return confirm('Are you Sure to delete?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan