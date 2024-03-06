@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
        </ul>
    </div>

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
                        <i class="fa fa-globe"></i>Batch Schedule List
                        @can('Batch Schedule Add')
                            <a href="{{url('admin/batch-schedules/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>

     
                <div class="portlet-body">
                    <div class="form">
                        <div class="form-body">
                            <!-- BEGIN FORM-->
                            {!! Form::open(['action'=>['Admin\BatchSchedulesController@master_schedule_list'],'files'=>true,'class'=>'form-horizontal']) !!}

                            <div class="form-group">
                                <label class="col-md-3 control-label">Start Date </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" id="start_date" name="start_date" value="{{ old('start_date') ?? '' }}" class="form-control input-append date" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">End Date </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" id="end_date" name="end_date" value="{{ old('end_date') ?? '' }}" class="form-control input-append date" autocomplete="off" required>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn btn-info">Search</button>
                                    <a href="{{ url('admin/master-schedule') }}" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <div class="row">

                    </div>                    
                    
                    <table style="width: 100%;">
                        <style>

                            .name-italic
                            {
                                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                font-size: 15px;
                                font-weight: 700;
                                font-style: italic;
                            }
                            .name-mentor
                            {
                                font-size: 15px;
                                font-weight: 700;
                                color:blue;
                            }
                            .name-batch
                            {
                                font-size: 15px;
                                font-weight: 700;
                                color:green;
                            }

                            .name-topic
                            {
                                font-size: 15px;
                                font-weight: 700;
                                color:blueviolet;
                            }

                            .name-exam
                            {
                                font-size: 15px;
                                font-weight: 700;
                                color:magenta;
                            }

                        </style>                        
                        <table class="table table-striped table-bordered table-hover datatable" style="width: 100%;">  
                            <thead>
                                <tr>
                                    <th>Serialwise Date</th>
                                    <th>Date In Human Readable Format</th>
                                    <th>Slot</th>
                                    @foreach($roomss as $room)
                                    <th data-id="{{ $room->id }}">{{ $room->name }}</th>
                                    @endforeach
                                    
                                </tr>                            
                            </thead>
                            <tbody>
                                @php $m = 0; @endphp
                                @foreach($master_schedules as $k=>$master_schedule)
                                    <tr>
                                        <td>{{ date("Y-m-d ", $k) }}</td>
                                        <td>{!! date("d M Y", $k).'<br>'.Date('l',$k) !!}</td>
                                        <td>{{ date("h:m:s a", $k) }}</td>
                                        @foreach($roomss as $room)
                                        @if($room->id == $master_schedule->schedule->room_id)
                                            <td>                                             
                                                <br><span class="name-batch">@php echo 'Batch : <span class="name-italic">'.$master_schedule->schedule->batch->name.'</span>'; @endphp</span>
                                                @foreach($master_schedule->details as $details) 
                                                @if($details->type == "Class")
                                                <br><span class="name-topic">@php echo 'Topic : <span class="name-italic">'.($details->class->name??'').'</span>' @endphp</span>
                                                <br><span class="name-mentor">@php echo 'Mentor : <span class="name-italic">'.($details->mentor->name??'').'</span>' @endphp</span>  
                                                @endif
                                                @if($details->type == "Exam")
                                                <br><span class="name-exam">@php echo 'Exam : <span class="name-italic">'.($details->exam->name??'').'</span>' @endphp</span>   
                                                @endif
                                                @endforeach                                            
                                            </td>
                                        @else
                                        <td></td>
                                        @endif
                                        @endforeach
                                    </tr>
                                @endforeach                            
                            </tbody>

                        </table>
                    </table>
                    
                </div>

            </div>
        </div>



@endsection

@section('js')

    

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>


    <script type="text/javascript">

        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $("body").on( "focus", ".date", function() {
                $(this).datepicker({
                    format: 'yyyy-mm-dd',
                    startDate: '',
                    endDate: '',
                }).on('changeDate', function(e){
                    $(this).datepicker('hide');
                });
            })

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }); 


            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $(this).val();
                var year = $('.year').val();

                $.ajax({
                    type: "POST",
                    url: '/admin/session-searching',
                    dataType: 'HTML',
                    data: {course_id : course_id },
                    success: function( data ) {
                         $('.session').html(data); 
                    }
                });

            })

             $("body").on( "change", "[name='session_id']", function() {
                var session_id = $(this).val();
                var course_id = $('.course_id').val();
                var year = $('.year').val();

                $.ajax({
                    type: "POST",
                    url: '/admin/batch-searching',
                    dataType: 'HTML',
                    data: {session_id : session_id, course_id : course_id, year : year },
                    success: function( data ) {
                         $('.batch').html(data); 
                    }
                });
            })


            $('.datatable').DataTable({
                processing: true,
                scrollY: 500,
                scrollX: 500,
                scrollCollapse: true,
                paging: false,
                columnDefs: [
                                { width: '500px', targets: 0 }
                            ]
            });
            
            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });
    
            $('.batch2').select2();
        })



    </script>

@endsection