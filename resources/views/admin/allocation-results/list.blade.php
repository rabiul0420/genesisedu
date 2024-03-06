@extends('admin.layouts.app')
@section('allocation-results', 'active')
@section('content')

    @if(Session::has('message'))
        <div class="alert alert-success" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif


    <style>
        .print-button {
            background-color: #4CAF50; /* Green */
            border: none;
            color: white;
            width: 30px;
            height: 20px;
            padding: -5px 5px 5px 5px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 20px;
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i>Allocation Result List <a class="print-button" href="/admin/allocation-results/print/{{$course->id}}/{{$exam->id}}/{{$discipline}}" ><i class="fa fa-print"></i></a>
                        
                    </div>
                    
                </div>
                <div>
                    <div class="caption">
                        
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th class="text-center">SL</th>
                            <th class="text-center">Reg No</th>
                            <th class="text-center">Doctor Name</th>
                            <th class="text-center">Candidate Type</th>
                            <th class="text-center">Course Id</th>
                            <th class="text-center">Institute</th>
                            <th class="text-center">Discipline</th>
                            <th>Doctor</th>
                            <th class="text-center">Mark</th>
                            <th>Allocated Institute</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php $sl = 0; @endphp
                            @foreach($allocated_private as $result)
                            @if(!$result->allocated_institute) @continue @endif
                            <tr>
                                <td>{{ ++$sl }}</td>
                                <td>{{ $result->doctor_course->reg_no ?? '' }}</td>
                                <td>{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->candidate_type ?? '' }}</td>
                                <td>{{ $result->doctor_course->id ?? '' }}</td>
                                <td>{{ $result->doctor_course->institute->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->subject->name ?? '' }}</td>
                                <td class="text-left">{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->obtained_mark ?? 0 }}</td>
                                <td class="text-left">{{ $result->allocated_institute ?? '' }}</td>
                            </tr>
                            @endforeach
                            @foreach($allocated_government as $result)
                            @if(!$result->allocated_institute) @continue @endif
                            <tr>
                                <td>{{ ++$sl }}</td>
                                <td>{{ $result->doctor_course->reg_no ?? '' }}</td>
                                <td>{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->candidate_type ?? '' }}</td>
                                <td>{{ $result->doctor_course->id ?? '' }}</td>
                                <td>{{ $result->doctor_course->institute->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->subject->name ?? '' }}</td>
                                <td class="text-left">{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->obtained_mark ?? 0 }}</td>
                                <td class="text-left">{{ $result->allocated_institute ?? '' }}</td>
                            </tr>
                            @endforeach
                            @foreach($allocated_bsmmu as $result)
                            @if(!$result->allocated_institute) @continue @endif
                            <tr>
                                <td>{{ ++$sl }}</td>
                                <td>{{ $result->doctor_course->reg_no ?? '' }}</td>
                                <td>{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->candidate_type ?? '' }}</td>
                                <td>{{ $result->doctor_course->id ?? '' }}</td>
                                <td>{{ $result->doctor_course->institute->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->subject->name ?? '' }}</td>
                                <td class="text-left">{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->obtained_mark ?? 0 }}</td>
                                <td class="text-left">{{ 'BSMMU' }}</td>
                                <td class="text-left">{{ $result->allocated_institute ?? '' }}</td>
                            </tr>
                            @endforeach
                            @foreach($allocated_armed_forces as $result)
                            @if(!$result->allocated_institute) @continue @endif
                            <tr>
                                <td>{{ ++$sl }}</td>
                                <td>{{ $result->doctor_course->reg_no ?? '' }}</td>
                                <td>{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->candidate_type ?? '' }}</td>
                                <td>{{ $result->doctor_course->id ?? '' }}</td>
                                <td>{{ $result->doctor_course->institute->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->subject->name ?? '' }}</td>
                                <td class="text-left">{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->obtained_mark ?? 0 }}</td>
                                <td class="text-left">{{ 'Armed Forces' }}</td>
                                <td class="text-left">{{ $result->allocated_institute ?? '' }}</td>
                            </tr>
                            @endforeach
                            @foreach($allocated_others as $result)
                            @if(!$result->allocated_institute) @continue @endif
                            <tr>
                                <td>{{ ++$sl }}</td>
                                <td>{{ $result->doctor_course->reg_no ?? '' }}</td>
                                <td>{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->candidate_type ?? '' }}</td>
                                <td>{{ $result->doctor_course->id ?? '' }}</td>
                                <td>{{ $result->doctor_course->institute->name ?? '' }}</td>
                                <td>{{ $result->doctor_course->subject->name ?? '' }}</td>
                                <td class="text-left">{{ $result->doctor_course->doctor->name ?? '' }}</td>
                                <td>{{ $result->obtained_mark ?? 0 }}</td>
                                <td class="text-left">{{ 'Others' }}</td>
                                <td class="text-left">{{ $result->allocated_institute ?? '' }}</td>
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
            })
        })
    </script>

@endsection
