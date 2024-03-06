@extends('admin.layouts.app')
@section('allocation-results', 'active')
@section('content')

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>
                        <strong>{{ $course_title ?? '' }}</strong>
                        Allocation Result List
                    </div>
                </div>
                <div>
                    <div class="caption">
                    </div>

                </div>
                <div class="portlet-body">
                    <div class="row">

                        <div class="form-group col-sm-2 form-group-lg ">
                            <label>Select Course</label>
                            <select onchange="changeCourse.call(this)" class="form-control">
                                <option value="">--all courses--</option>
                                @foreach($courses as $course_id => $course )
                                    <option value="{{$course_id}}" {{ request()->course_id == $course_id ? 'selected':''  }}>{{$course}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="#loading" style="display: none; text-align: center; margin: 10px 0">Loading</div>

                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Exam Name</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Session</th>
                            <th>Mark/Time(Minutes)</th>
                            <th>SIF only?</th>
                            <th width="100">Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($exams as $exam)
                            <tr>
                                <td>{{ $exam->id }}</td>
                                <td class="text-left">{{ $exam->name ?? '' }}</td>
                                <td class="text-left">{{ $exam->institute->name ?? '' }}</td>
                                <td class="text-left">{{ $exam->course->name ?? '' }}</td>
                                <td>{{ $exam->year }}</td>
                                <td class="text-left">{{ $exam->sessions->name }}</td>
                                <td>
                                    {{ $exam->question_type->full_mark ?? '' }} / {{ ( $exam->question_type->duration ?? 0 ) / 60 }}
                                </td>
                                <td>{{ ($exam->sif_only=='Yes') ? 'Yes' : 'No' }}</td>
                                <td>
                                    <a href="{{ route('allocation-results.show', [$exam->id] ) }}" class="btn btn-xs btn-info">View Result</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <script type="text/javascript">
        function changeCourse() {
            $("#loading").css('display', 'block')
            if( this.value == '') {
                window.location = '/admin/allocation-results'
            }else {
                window.location = '/admin/allocation-results?course_id=' + this.value
            }
        }

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
            })
        })
    </script>

@endsection
