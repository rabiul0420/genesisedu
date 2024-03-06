<a  target="_blank" href="{{ url('admin/batches-schedules/print-batch-schedule/'.$batch_schedule_list->id) }}" class="btn btn-xs btn-primary"><i class="fa fa-print"></i> Print</a>
<a href="{{ url('admin/batches-schedules/'.$batch_schedule_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
{!! Form::open(array('route' => array('batches-schedules.destroy', $batch_schedule_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}