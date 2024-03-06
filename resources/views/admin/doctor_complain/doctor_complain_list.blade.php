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
                        <a href="{{url('admin/complain/create')}}"> <i class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>

                <div class="portlet-body">
                    <div class="row sc_search">
                        @include('admin.components.year_course_session')
    
                        <div class="batch">
                        </div>
                    </div>
                </div>  

                <div class="portlet-body">
                    <div class="row sc_search">
                        <div class="form-group col-md-2">
                            <h5>Complain Types <span class="text-danger"></span></h5>
                            <div class="controls">
                                @php  $complain_types->prepend('Select Complain Types', ''); @endphp
                                {!! Form::select('complain_type_id',$complain_types, '' ,['class'=>'form-control batch2','required'=>'required','id'=>'complain_type_id']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group col-md-2">
                            <h5>Last Reply<span class="text-danger"></span></h5>
                            <select name="reply" id="reply" class="form-control">
                                <option value=""> Select Reply</option>
                                <option value="Yes"> Yes </option>
                                <option value="No">No</option>
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label>Start Date </label>
                            <div class="controls">
                                <input type="date"  size="20" class="form-control" id="from"  name="start_date">
                            </div>
                        </div>
                        <div class="form-group col-md-2">
                            <label>End Date </label>
                            <div class="controls">
                                <input type="date"  size="20" class="form-control" id="to" name="end_date">
                            </div>
                        </div>
                                              
                    </div>
                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Search</button>
                    </div>
                    

                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th class="text-center" width="100">#</th>
                            <th class="text-center">Doctor</th>
                            <th class="text-center">BMDC No</th>
                            <th class="text-center">Mobile</th>
                            <th class="text-center">Batch Name</th>
                            <th class="text-center">course Name</th>
                            <th class="text-center">Complain Related</th>
                            <th class="text-center">Last is read</th>
                            <th class="text-center">Complain Create Time</th>
                            <th class="text-center">Last Reply Date</th>
                            <th class="text-center" width="140">Action</th>
                        </tr>
                        </thead>
                 
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')

<script type="text/javascript">
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("body").on( "change", "[name='session_id']", function() {
            var session_id = $(this).val();
            var course_id = $('#course_id').val();
            var year = $('#year').val();

            $.ajax({
                type: "POST",
                url: '/admin/search-batch',
                dataType: 'HTML',
                data: {session_id : session_id, course_id : course_id, year : year },
                success: function( data ) {
                        $('.batch').html(data); 
                        $('#batch_id').select2();
                }
            });
        })

        $('.datatable').DataTable({
            order : [[4, "ASC"],[5,"DESC"]],
            responsive: true,
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/doctor-complain-ajax-list',
                type: 'GET',
                data: function (d) {
                    d.year = $('#year').val();
                    d.session_id = $('#session_id').val();
                    d.course_id = $('#course_id').val();
                    d.batch_id = $('#batch_id').val();
                    d.complain_type_id = $('#complain_type_id').val();
                    d.reply = $('#reply').val();
                    d.start_date = $('#from').val();
                    d.end_date = $('#to').val();
                }
            },
            
            "pageLength": 25,
            columns: [
                {data: 'complain_id',name:'d1.id'},
                {data: 'doctor_name',name:'d2.name'},
                {data: 'doctor_bmdc_no',name:'d2.bmdc_no'},
                {data: 'doctor_mobile_number',name:'d2.mobile_number'},
                {data: 'batch_name',name:'b.name'},
                {data: 'course_name',name:'c.name'},
                {data: 'complain_related'},
                {data: 'last_reply_status', name:'d1.last_reply_status'},
                {data: 'complain_create_time',searchable: false },
                {data: 'last_reply_time',name:'d1.last_reply_time'},
                // {data: 'user_name',name:'u.name'},
                {data: 'action', searchable: false },
            ],
        });

        $('#btnFiterSubmitSearch').click(function(){
            $('.datatable').DataTable().draw(true);
        });
        $('#year').select2();
    })
</script>

@endsection
