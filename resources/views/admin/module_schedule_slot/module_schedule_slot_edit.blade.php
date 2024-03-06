@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'"> '.$value.' </a></li>';
            }
            ?>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{ $module_name }} Create
                        @can('Module Schedule Slot')
                        <a href="{{ url('admin/module-schedule-slot-list/'.$module_schedule_slot->module_schedule->id) }}" class="btn btn-xs btn-primary">Module Schedule Slot</a>
                        @endcan
                    </div>
                </div>

                <div class="portlet-body">
                    <input type="hidden" name="module_schedule_id" value="{{ $module_schedule_slot->module_schedule->id }}"/>
                    <input type="hidden" name="module_schedule_slot_id" value="{{ $module_schedule_slot->id }}"/>
                    <div class="row">
                        <div class="form-group col-md-2">
                            <h5>Branch <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $branches->prepend('Select Branch', ''); @endphp
                                {!! Form::select('branch_id',$branches, $module_schedule_slot->slot->room->branch_id??'' ,['class'=>'form-control select2','required'=>'required','id'=>'branch_id']) !!}<i></i>
                            </div>
                        </div>
                        <div class="location">
                            <div class="form-group col-md-2">
                                <h5>Location <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $locations->prepend('Select Location', ''); @endphp
                                    {!! Form::select('location_id',$locations, $module_schedule_slot->slot->room->location_id??'' ,['class'=>'form-control select2','required'=>'required','id'=>'location_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>
                        <div class="floor">
                            <div class="form-group col-md-2">
                                <h5>Floor <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $floors->prepend('Select Floor', ''); @endphp
                                    {!! Form::select('floor',$floors, $module_schedule_slot->slot->room->floor??'',['class'=>'form-control select2','required'=>'required','id'=>'year']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="room">
                            <div class="form-group col-md-2">
                                <h5>Room <span class="text-danger"></span></h5>
                                <div class="controls">
                                    @php  $rooms->prepend('Select Room', ''); @endphp
                                    {!! Form::select('room_id',$rooms, $module_schedule_slot->slot->room->id??'',['class'=>'form-control select2','required'=>'required','id'=>'room_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="class_date">
                            <div class="form-group col-md-2">
                                <h5>Date <span class="text-danger"></span></h5>
                                <div class="controls">
                                    <input type="text" name="search_date" required value="" class="form-control timepicker" autocomplete="off">
                                </div>
                            </div>
                        </div>                    
                        
                                                
                    </div>
                    <div class="row">
                        <div class="form-group col-md-3">
                            <button type="text" id="btnsearch" class="btn btn-info">Search</button>
                        </div>
                    </div>
                    
                    <table id="table_1" class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Room</th>
                            <th>Date</th>
                            <th>Start Time - End Time</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        
                        </tbody>
                    </table>
                    
                    
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

@section('js')

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker.min.css" />
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" />
    <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>

    <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.js"></script>
    <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            // $('.timepicker').datetimepicker({
            //     format: 'LT',
            // });

            $("body").on( "focus", ".timepicker", function() {
                $(this).datetimepicker({
                    format: 'YYYY-MM-DD',
                    // Your Icons
                    // as Bootstrap 4 is not using Glyphicons anymore
                    icons: {
                        time: 'fa fa-clock-o',
                        date: 'fa fa-calendar',
                        up: 'fa fa-chevron-up',
                        down: 'fa fa-chevron-down',
                        previous: 'fa fa-chevron-left',
                        next: 'fa fa-chevron-right',
                        today: 'fa fa-check',
                        clear: 'fa fa-trash',
                        close: 'fa fa-times'
                    }
                });
            });

            $(document).on('change', '[name="slot_id"]', function() {
                
                var operation = "";
                if(this.checked == true)
                {
                    operation = "insert";      
                }
                else if(this.checked == false)
                {
                    operation = "delete";
                }

                var slot_id = $(this).val();
                var module_schedule_id = $('[name="module_schedule_id"]').val();
                var module_schedule_slot_id = $('[name="module_schedule_slot_id"]').val();                           

                $.ajax({
                    type: "POST",
                    url: '/admin/module-schedule-slot-update',
                    dataType: 'HTML',
                    data: {slot_id : slot_id, module_schedule_id : module_schedule_id, module_schedule_slot_id : module_schedule_slot_id, operation : operation, _token: '{{ csrf_token() }}' },
                    success: function( data ) { 
                        var data = JSON.parse(data);
                        $("#label_"+data['slot_id']).html(data['message']);
                        
                        if(data['status'] == "insert_success")
                        {
                            $("#label_m_"+data['slot_id']).removeClass("btn btn-info").addClass("btn btn-danger");
                            $("#label_m_"+data['slot_id']).html("Delete From Module Schedule Slot");
                        }
                        if(data['status'] == "delete_success")
                        {
                            $("#label_m_"+data['slot_id']).removeClass("btn btn-danger").addClass("btn btn-info");
                            $("#label_m_"+data['slot_id']).html("Add to Module Schedule Slot");
                        }
                                                            
                        if(data['status'] == "completed" || data['status'] == "data_already_exist" )
                        {
                            window.location.href = "/admin/module-schedule-slot-list/"+data['module_schedule_id'];
                        }
                        
                    }
                });   
                
                
            });


            $("#table_1").on("mouseover", 'td' , function () {
                
                $(this).css('cursor','pointer');                        
                
            });

            $("#table_1").on("click", 'td' , function () {
                
                var slot_id = $(this).closest('tr').find('td').first().html();
                
                if(!isNaN(slot_id) && $(this).index() != ( $(this).closest('tr').children('td').length - 1 ) )
                {
                    $('#question_answer .modal-body').load('/admin/get-question-details',{slot_id : slot_id, _token: '{{ csrf_token() }}'},function(){
                        $('#question_answer').modal({show:true});
                    });
                }                                
                
            });

            var table = $('.datatable').DataTable({
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/module-schedule-slot-edit-list",
                    type: 'GET',
                    data: function (d) {
                        d.module_schedule_id = $('[name="module_schedule_id"]').val();
                        d.branch_id = $('[name="branch_id"]').val();
                        d.location_id = $('[name="location_id"]').val();
                        d.floor = $('[name="floor"]').val();
                        d.room_id = $('[name="room_id"]').val();
                        d.search_date = $('[name="search_date"]').val();
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'id',name:'d1.id'},
                    {data: 'name',name:'d2.name'},
                    {data: 'date',name:'date'},
                    {data: 'start_time_end_time',name:'start_time_end_time'},
                    {data: 'action',searchable: false},
                ]
            })
            $('#btnsearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });

            $("body").on( "change", "[name='branch_id']", function() {
                var branch_id = $(this).val();
                var view_name = 'room_branch_location_search';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/branch-change-in-room',
                    dataType: 'HTML',
                    data: {branch_id : branch_id,view_name:view_name},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.location').html('');
                        $('.floor').html('');
                        $('.room').html('');
                        $('.capacity').html('');
                        $('.location').html(data['location']);
                    }
                });
            });

            $("body").on( "change", "[name='location_id']", function() {
                var branch_id = $("[name='branch_id']").val();
                var location_id = $(this).val();
                var view_name = 'room_location_floor_search';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/location-change-in-room',
                    dataType: 'HTML',
                    data: {branch_id : branch_id,location_id : location_id,view_name:view_name},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.floor').html('');
                        $('.room').html('');
                        $('.capacity').html('');
                        $('.floor').html(data['floor']);
                        $('.room').html(data['room']);
                        $('.capacity').html(data['capacity']);
                    }
                });
            });

            $("body").on( "change", "[name='floor']", function() {
                var branch_id = $("[name='branch_id']").val();
                var location_id = $("[name='location_id']").val();
                var floor = $(this).val();
                var view_name = 'room_floor_room_search';
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/floor-change-in-room',
                    dataType: 'HTML',
                    data: {branch_id : branch_id,location_id : location_id,floor:floor,view_name:view_name},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.room').html('');
                        $('.capacity').html('');
                        $('.room').html(data['room']);
                        $('.capacity').html(data['capacity']);
                    }
                });
            });

        })
    </script>


@endsection

