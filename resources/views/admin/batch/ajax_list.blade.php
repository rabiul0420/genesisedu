
@can('Batch Edit')
<a href="{{ url('admin/batch/'.$batch->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan
@can('Batch Delete')
    {!! Form::open(array('route' => array('batch.destroy', $batch->id), 'method' => 'delete','style' => 'display:inline')) !!}
    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}   
 @endcan
@if($batch->system_driven == "Mandatory" || $batch->system_driven == "Optional")
<a href="{{ url('admin/batch-system-driven/'.$batch->id) }}" class="btn btn-xs btn-info">System Driven</a>
@endif
<a href="{{ url('admin/results/'.$batch->id) }}" class="btn btn-xs btn-primary">Results</a>