@can('Batch Faculty Fee List Edit')
 <a href="{{ url('admin/batch-faculty-fee/'.$batch_faculty_fees_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a> 
@endcan
@can('Batch Faculty Fee List Duplicate')
 <a href="{{ url('admin/batch-faculty-fee/'.$batch_faculty_fees_list->id.'/duplicate') }}" class="btn btn-xs btn-primary">Duplicate</a>  
@endcan

{{-- {!! Form::open(array('route' => array('batch.destroy', $batch_faculty_fees_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!} --}}