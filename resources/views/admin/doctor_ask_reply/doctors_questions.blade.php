@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'">'.$value.'</a> </li>';
            }
            ?>
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
                        <i class="fa fa-globe"></i><?php echo $module_name;?> List
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">
                        
                        @include('admin.components.year_course_session_others')
                    
                    </div>

                    <div class="row sc_search">
                        <div class="batch">
                            <div class="form-group col-md-2">
                             <label>Batch <span class="text-danger"></span></label>
                                <div class="controls">
                                    @php  $batches->prepend('Select Batch', ''); @endphp
                                    {!! Form::select('batch_id',$batches, '' ,['class'=>'form-control select2','required'=>'required','id'=>'batch_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="lecture_video">
                            <div class="form-group col-md-2">
                                <label>Video Name <span class="text-danger"></span></label>
                                <div class="controls">
                                    @php  $lecture_video->prepend('Select Video', ''); @endphp
                                    {!! Form::select('lecture_video',$lecture_video, '' ,['class'=>'form-control batch2','required'=>'required','id'=>'video_id']) !!}<i></i>
                                </div>
                            </div>
                        </div> 

                        <div class="form-group col-md-1">
                            <label>Start Date <span class="text-danger"></span></label>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control" id="from"  name="start_date">
                            </div>
                        </div>
                        
                        <div class="form-group col-md-1">
                            <label>End Date <span class="text-danger"></span></label>
                            <div class="controls">
                                <input type="text"  size="20" class="form-control" id="to" name="end_date">
                            </div>
                        </div>

                    </div>    

                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                        <button type="text" id="print" class="btn btn-info">Print</button>
                    </div>

                        <table class="table table-striped table-bordered table-hover datatable">
                            <thead>
                            <tr>
                                <th width="100">ID</th>
                                <th>Year</th>
                                <th>Doctor</th>
                                <th>Course</th>
                                <th>Batch</th>
                                <th>Session</th>
                                <th>Video</th>
                                <th>Date & Time</th>
                                <th>Mentor</th>
                                <th width="90">Feedback</th>
                                <th width="140">Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    <script src="{{ asset('assets/scripts/jquery-ui.min.js') }}"></script>

    <script type="text/javascript">

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#batch_id").select2({
                allowClear: true
            });

            $("body").on( "change", "[name='year'],[name='course_id'],[name='session_id'],[name='faculty_id'],[name='subject_id']", function() {
                var session_id = $('#session_id').val();
                var course_id = $('#course_id').val();
                var batch_id = $('#batch_id').val();
                var fuculty_id = $('#fuculty_id').val();
                var subject_id = $('#subject_id').val();
                var year = $('#year').val();

                $.ajax({
                    type: 'POST',
                    url: '/admin/doctor-batch-search',
                    dataType: 'HTML',
                    data: {
                        session_id,
                        course_id,
                        batch_id,
                        subject_id,
                        fuculty_id,
                        year
                    },
                    success: function( data ) {
                        $('.batch').html(data);
                        $('#batch_id').select2();
                    }
                });
            });

            $("body").on( "change", "[name='batch_id'],[name='session_id'],[name='course_id'],[name='year'],[name='faculty_id'],[name='subject_id']", function() {
                var session_id = $('#session_id').val();
                var course_id = $('#course_id').val();
                var batch_id = $('#batch_id').val();
                var fuculty_id = $('#fuculty_id').val();
                var subject_id = $('#subject_id').val();
                var year = $('#year').val();

                    $.ajax({
                        type: "POST",
                        url: '/admin/doctor-video-search',
                        dataType: 'HTML',
                        data: {
                            session_id,
                            course_id,
                            batch_id,
                            subject_id,
                            fuculty_id,
                            year
                        },
                        success: function( data ) {
                             $('.lecture_video').html(data);
                             $("#video_id").select2();
                        }
                    });
            })

            var table = $('.datatable').DataTable({
                order: [ [0, 'DESC'] ],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/doctors-complain-list",
                    type: 'GET',
                    data: function ( d ) {
                        d['year'] = $('[name="year"]').val( );
                        d['session_id'] = $('[name="session_id"]').val( );
                        d['course_id'] = $('[name="course_id"]').val( );
                        d['batch_id'] = $('[name="batch_id"]').val( );
                        d['subject_id'] = $('[name="subject_id"]').val( );
                        d['faculty_id'] = $('[name="faculty_id"]').val( );
                        d['lecture_video'] = $('[name="lecture_video"]').val( );
                        d['start_date'] = $('[name="start_date"]').val( );
                        d['end_date'] = $('[name="end_date"]').val( );
                    }
                },
                pageLength: 10,
                columns: [
                    { data: 'doctor_ask_id', name:'da.id' },
                    { data: 'year', name:'dc.year' },
                    { data: 'doctor_name', name:'d.name' },
                    { data: 'course_name', name:'c.name' },
                    { data: 'batch_name', name:'b.name' },
                    { data: 'session_name', name:'s.name' },
                    { data: 'lecture_video', name:'lv.name' },
                    { data: 'date_and_time', name:'da.created_at' },
                    { data: 'teacher_name', name:'t.tec_name' },
                    { data: 'has_feedback' , searchable: false, sortable: false, render: function( data, type, row ) {
                        // return '<div>Fed: ' +( data == null ? 'YES':'NO') + '</div>';
                        return '' +
                            '<div class="feedback" style="display: flex">'+

                               '<button class="btn btn-sm '
                                    + ( String(data).toLowerCase() == 'yes' ? 'btn-success':'' )
                                    + '" style="margin-right: 8px" data-value="yes" data-id="'+row.doctor_ask_id+'">Yes</button>' +

                               '<button class="btn btn-sm '
                                    + ( String(data).toLowerCase() == 'no' ? 'btn-danger':'' )
                                    + '"  data-value="no" data-id="'+row.doctor_ask_id+'">No</button>' +

                            '</div>';
                    }},
                    { data: 'action',searchable: false, sortable: false },
                ]
            })

            table.on('draw', function ( ){

               $('.feedback .btn').on( 'click', function ( ){
                   const value = $( this ).data('value');
                   const id = $( this ).data('id');
                   $('[data-id="' + id + '"]').attr( 'disabled', true );
                   $.ajax({
                       type: "POST",
                       url: '/admin/doctor-question-feedback',
                       dataType: 'JSON',
                       data: { value, id },
                       success: function( data ) {
                            if( data.changed ) {
                               table.draw( 'page' );
                            }else {
                                $('[data-id="' + id + '"]').attr( 'disabled', false );
                            }
                       }
                   });

               });

            });

            
            $('#btnFiterSubmitSearch').click( () => table.draw( true ) );

            $('.batch2').select2();

            $('#print').click(function(){


                var year = $('[name="year"]').val();
                var session = $('[name="session_id"]').val();
                var batch = $('[name="batch_id"]').val();
                var video = $('[name="lecture_video"]').val();
                var start_date = $('[name="start_date"]').val();
                var end_date = $('[name="end_date"]').val();

                var params = '?year='+year+'&session_id='+session+'&batch_id='+batch +'&lecture_video=' + video + '&start_date=' + start_date + '&end_date=' + end_date;

                if( video && start_date && end_date){

                    var pw = window.open( "/admin/doctor-questions-print"+params, '_blank',
                        "toolbar=yes,scrollbars=yes,resizable=yes,top=50,left=50,width=1000,height=800" );
                    pw.print( );
                    // pw.close( );

                } else {
                     alert('Please select a video, start date and end date');
                }
            });

            [ ['#from','#to','+0d','minDate'], ['#to','#from','+1d', 'maxDate'] ].map(item => {
                $( item[0] ).datepicker({
                    defaultDate: item[2],
                    changeMonth: true,
                    dateFormat: 'yy-mm-dd',
                    numberOfMonths: 1,
                    onClose: function( selectedDate ) {
                        $( item[1] ).datepicker( "option", item[3], selectedDate );
                    }
                });
            });

        })

    </script>

@endsection
