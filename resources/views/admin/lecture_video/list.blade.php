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
            <p> {!! Session::get('message')  !!} </p>
        </div>
    @endif

    <style>
        .empty-lecture-video td {
            background-color:#FA9 !important;
            border-right-color: #ffc3b8 !important;
        }
    </style>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-globe"></i><?php echo $module_name;?> List
                        @can('Lecture Video')
                        <a href="{{url('admin/lecture-video/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">
                        @include('admin.components.year_course_session') 

                        
                        <div class="form-group col-md-2">
                            <label class="col-md-3 control-label">Price</label>
                            <div class="controls">
                                <select name="price" id="price" class="form-control">
                                    <option value="">All</option>
                                    <option value="1">With Price</option>
                                    <option value="0">Without Price</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Filter</button>
                        <!-- <button type="text" id="print" class="btn btn-info">Print</button> -->
                    </div>

                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Class/Topic</th>
                                <th>Year</th>
                                <th>Course</th>
                                <th>Session</th>
                                <th>Type</th>
                                <th>Mentor</th>
                                <th>Play</th>
                                <th>Video Address</th>
                                <th>Video Password</th>
                                <th>Video PDF</th>
                                {{-- <th>InSubscription</th> --}}
                                <th>Status</th>
                                <th>Action</th>
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/lecture-video-list",
                    type: 'GET',
                    data: function (d) {
                        d.price = $('#price').val();
                        d.year = $('#year').val();
                        d.session_id = $('#session_id').val();
                        d.course_id = $('#course_id').val();
                    }
                },
                "pageLength": 15,
                columns: [
                    {data: 'id', name:'d1.id'},
                    {data: 'lecture_video_name', name:'d1.name'},
                    {data: 'class_name', name:'d2.name'},
                    {data: 'year', name:'d2.year'},
                    {data: 'course_name', name:'c.name'},
                    {data: 'session_name', name:'s.name'},
                    {data: 'type_name', name:'d1.type'},
                    {data: 'teacher_name', name:'t.name'},
                    {data: 'play', searchable: false},
                    {data: 'lecture_link_address', name:'d1.lecture_address'},
                    {data: 'video_password', name:'d1.password'},
                    {data: 'pdf_file_link', name:'d1.pdf_file'},
                    // {data: 'is_show_subscription', name:'d1.is_show_subscription'},
                    {data: 'status', searchable: false},
                    {data: 'action', searchable: false},
                ]
            })

            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });
        })
    </script>

@endsection
