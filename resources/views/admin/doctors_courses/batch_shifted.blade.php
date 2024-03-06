
{{-- @can('Doctor Course Delete')
{!! Form::open(array('route' => array('doctors-courses.destroy', $doctors_courses_list->id), 'method' => 'delete','style' => 'display:inline')) !!}
<button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
{!! Form::close() !!}
@endcan --}}
{{-- @can('Doctor Course Payment View')
<button class="btn btn-xs btn-primary payment" data-toggle='modal' id="{{$doctors_courses_list->id}}" data-target='#myModal_{{$doctors_courses_list->id}}'>Payment</button>
@endcan --}}

<button class="btn btn-xs btn-primary {{ $doctors_courses_list->batch_shifted  == 'Yes'? 'batch_shifted' : ' ' }} " data-toggle='modal' id="{{$doctors_courses_list->id}}" data-target='#myModal_{{$doctors_courses_list->id}}'>{{ $doctors_courses_list->batch_shifted }}</button>
    {{-- <a href="{{ url('admin/doctor-course-lecture-sheet-list/'.$doctors_courses_list->id) }}"  class="btn btn-xs ">{{ $doctors_courses_list->batch_shifted  == '1'? 'YES' : 'NO' }}</a> --}}
