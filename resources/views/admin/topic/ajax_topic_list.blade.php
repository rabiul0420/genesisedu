@can('Class/Chapter Edit')
    <a href="{{ url('admin/topic/'.$class_chapter_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
    <a href="{{ url('admin/topic/'.$class_chapter_list->id.'/duplicate') }}" class="btn btn-xs btn-success">Duplicate</a>
@endcan
@can('Class/Chapter Delete')
    {!! Form::open(array('route' => array('topic.destroy', $class_chapter_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
        <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
@endcan
