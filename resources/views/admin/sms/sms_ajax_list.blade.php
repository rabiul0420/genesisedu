@can('Notice')
    <a href="{{ url('admin/sms-show/'.$sms_list->id) }}" class="btn btn-xs btn-primary">View</a>
@endcan

@can('Notice')
    <a href="{{ url('admin/sms/'.$sms_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan

@can('Notice')
    {!! Form::open(array('route' => array('sms.destroy', $sms_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
@endcan