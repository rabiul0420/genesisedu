@can('Notice')
    <a href="{{ url('admin/notice_show/'.$notice_list->id) }}" class="btn btn-xs btn-primary">View</a>
@endcan

@can('Notice')
    <a href="{{ url('admin/notice/'.$notice_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
@endcan

@can('Notice')
    {!! Form::open(array('route' => array('notice.destroy', $notice_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
@endcan