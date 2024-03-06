<div style="width: 220px; display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
    @can('Doctor Course Edit')
    <a href="{{ url('admin/doctors-courses/'.$doctors_courses_list->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
    <!-- <a href="{{ url('admin/doctor-course-active-list/' . $doctors_courses_list->id . '/quick-edit') }}" class="btn btn-xs btn-primary">
        Quick Edit
    </a> -->
    @endcan

    @can('Doctor Course Delete')
    {!! Form::open(array('route' => array('doctors-courses.destroy', $doctors_courses_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
    {!! Form::close() !!}
    @endcan

    @can('Doctor Course Payment View')
    <button class="btn btn-xs btn-primary payment" data-toggle='modal' id="{{$doctors_courses_list->id}}" data-target='#myModal_{{$doctors_courses_list->id}}'>Payment</button>
    @endcan

    @can('Doctor Course Result')
    <a href="{{ url('admin/view-course-result/'.$doctors_courses_list->id) }}" class="btn btn-xs btn-primary">All Result</a>
    @endcan

    @can('Doctor Course Payment View')
    @if($doctors_courses_list->include_lecture_sheet == '1')
    <a href="{{ url('admin/doctor-course-lecture-sheet-list/'.$doctors_courses_list->id) }}" target="_blank" class="btn btn-xs {{ ($doctors_courses_list->lecture_sheet_delivery_status == "Not_Delivered") ? 'btn-danger' : (($doctors_courses_list->lecture_sheet_delivery_status == "Completed")?'btn-success':'btn-info') }}">Lecture Sheet</a>
    @endif
    @endcan

    @can('Doctor Course Details')
    <button class="btn btn-xs btn-primary print_doctor" data-toggle='modal' id="{{$doctors_courses_list->id}}" data-target='#myModal_{{$doctors_courses_list->id}}'>Course Details</button>
    @endcan

    @if($doctors_courses_list->batch_shifted)
    <button 
        data-toggle='modal'
        id="{{$doctors_courses_list->id}}"
        data-target='#myModal_{{$doctors_courses_list->id}}'
        class="btn btn-xs btn-warning batch_shifted"
    >
        {{ $doctors_courses_list->batch_shifted == "1" ? 'Shifted' : '' }}
    </button>
    @endif
</div>
 