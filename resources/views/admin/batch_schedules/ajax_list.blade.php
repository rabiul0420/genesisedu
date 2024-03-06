{{--<a  target="_blank" href="{{ url('admin/batches-schedules/print-batch-schedule/'.$batch_schedule_list->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-print"></i> Print</a>--}}
<a href="{{ url('admin/batch-schedules/'.$batch_schedule_list->id.'/show') }}" class="btn btn-xs btn-primary">Show</a>

<a href="{{ url('admin/batch-schedules/'.$batch_schedule_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
<a href="{{ url('admin/batch-schedules/'.$batch_schedule_list->id.'/duplicate') }}" class="btn btn-xs btn-warning">Duplicate</a>

@can('New Batch Schedule Delete')
{!! Form::open(array('route' =>  array('batches-schedules.destroy', $batch_schedule_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
   <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan

