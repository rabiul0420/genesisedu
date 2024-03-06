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
                        <i class="fa fa-globe"></i>Subscription Video List
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="row sc_search">
                        @include('admin.components.year_course_session')
                    </div>

                    <div class="text-center" style="margin-left: 15px;">
                        <button type="text" id="btnFiterSubmitSearch" class="btn btn-info">Filter</button>
                        <!-- <button type="text" id="print" class="btn btn-info">Print</button> -->
                        <button type="text" id="MarkedSubscribe" data-id="1" class="btn btn-success">Marked Subscribe</button>
                        <button type="text" id="MarkedUnsubscribe" data-id="0" class="btn btn-danger">Marked Unsubscribe</button>                     
                    </div>

                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="checkall" id="checkall" onClick="check_uncheck_checkbox(this.checked);" /> Check All</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Class/Topic</th>
                                <th>Subscription</th>
                                <th>Action</th>
                                <th>Play</th>
                                <th>Video Address</th>
                                <th>Password</th>
                                <th>Year</th>
                                <th>Corse</th>
                                <th>Session</th>
                                <th>Type</th>
                                <th>Mentor</th>
                                {{-- <th>Video PDF</th> --}}
                                {{-- <th>Status</th> --}}
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('js')
    <script>
        function subscriptionStatus(button, videoId) {
            if(confirm("Do you want to change?")) {

                axios.post(`/admin/subscription-status/${videoId}`)
                    .then((res) => {
                        button.innerHTML = res.data.status ? 'Yes' : 'No';
                        button.classList.remove('btn-info');
                        button.classList.remove('btn-danger');
                        button.classList.add(res.data.status ? 'btn-info' : 'btn-danger');
                    })
            }
        }

            function check_uncheck_checkbox(isChecked) {  
        if(isChecked) {
            $('input[name="language"]').each(function() { 
                this.checked = true; 
            });
        } else {
            $('input[name="language"]').each(function() {
                this.checked = false;
            });
        }
     }

    </script>

    <script type="text/javascript">

              $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

        $(document).on('click', '#MarkedSubscribe, #MarkedUnsubscribe', function() {   
            
            var subscription = $(this).data('id');
            
            var employee = [];  
            $(".subscriptionUpdate:checked").each(function() {  
                employee.push($(this).data('lecture-video-id'));
            });	
            if(employee.length <=0)  {  
                alert("Please select records.");  
            } 
            else { 	
		WRN_PROFILE_DELETE = "Are you sure you want to change "+(employee.length>1?"these":"this")+" row?";  
		var checked = confirm(WRN_PROFILE_DELETE);  
		if(checked == true) {			
			var selected_values = employee.join(","); 
			$.ajax({ 
				type: "POST",  
				url: "marked-subscription-video-update",  
				cache:false,  
				data: {video_id : selected_values,subscription:subscription },  
				success: function(response) {	

                    location.reload();
												
				}   
			});				
		}  
	}  
});










        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                order : [[0, 'DESC']],
                responsive: true,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/admin/subscription-video-list",
                    type: 'GET',
                    data: function (d) {
                        d.year = $('#year').val();
                        d.session_id = $('#session_id').val();
                        d.course_id = $('#course_id').val();
                    }
                },
                "pageLength": 10,
                columns: [
                    {data: 'allchecked', searchable: false, sortable: false},
                    {data: 'id', name:'d1.id'},
                    {data: 'lecture_video_name', name:'d1.name'},
                    {data: 'class_name', name:'d2.name'},
                    {data: 'is_show_subscription', name:'d1.is_show_subscription'},
                    {data: 'action', searchable: false},
                    {data: 'play', searchable: false},
                    {data: 'lecture_link_address', name:'d1.lecture_address'},
                    {data: 'video_password', name:'d1.password'},
                    {data: 'year', name:'d2.year'},
                    {data: 'course_name', name:'c.name'},
                    {data: 'session_name', name:'s.name'},
                    {data: 'type_name', name:'d1.type'},
                    {data: 'teacher_name', name:'t.name'},
                    // {data: 'pdf_file_link', name:'d1.pdf_file'},
                    // {data: 'status', searchable: false},
                ]
            })
      
            $('#btnFiterSubmitSearch').click(function(){
                $('.datatable').DataTable().draw(true);
            });
        })
    </script>

@endsection
