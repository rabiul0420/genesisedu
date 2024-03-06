@can('Batch Discipline Fee Edit')
<a href="{{ url('admin/batch-discipline-fee/'.$batch_discipline_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Batch Discipline Fee Duplicate')
<a href="{{ url('admin/batch-discipline-fee/'.$batch_discipline_list->id.'/duplicate') }}" class="btn btn-xs btn-primary">Duplicate</a>
@endcan

{{-- {!! Form::open(array('route' => array('batch.destroy', $batch_discipline_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!} --}}