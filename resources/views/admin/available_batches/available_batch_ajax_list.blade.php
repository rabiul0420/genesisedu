@can('Available Batch Edit')
    <a href="{{ url('admin/available-batches/'.$available_batche_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
    <a href="{{ url('admin/available-batches/'.$available_batche_list->id.'/duplicate') }}" class="btn btn-xs btn-primary">Duplicate</a>
@endcan
@can('Available Batch Delete')
    {!! Form::open(array('route' => array('available-batches.destroy', $available_batche_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Move to trash</button>
    {!! Form::close() !!}
@endcan