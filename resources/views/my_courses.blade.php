@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'My Courses' }}</h2>
                    </div>
                </div>

                <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <?php if (isset($_GET['msgs'])) { echo "<div class='alert alert-success'>{$_GET['msgs']}</div>"; } ?>
                        <?php if (isset($_GET['msgf'])) { echo "<div class='alert alert-success' style='color:red;'>{$_GET['msgf']}</div>"; } ?>
                        <?php if (isset($_GET['msgc'])) { echo "<div class='alert alert-success' style='color:red;'>{$_GET['msgc']}</div>"; } ?>

                        <div class="col-md-12 py-3 px-0">

                            <table class="bg-white table text-center table-striped table-bordered rounded p-1 table-hover datatable">
                                <thead>
                                <tr>
                                    <th style="width: 50px;">SL</th>
                                    <th>Reg. No.</th>
                                    <th>Actions</th>
                                    <th>Candidate Type</th>
                                    <th>Year</th>
                                    <th>Session</th>
                                    <th>Course</th>
                                    <th>Discipline</th>
                                    <th>Batch</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach ($doc_info->doctorcourses as $k=>$value)
                                    @if($value->is_trash == '0' && $value->status == 1)
                                    <tr>
                                        <td>{{ $k+1 }}</td>
                                        <td>{{ $value['reg_no'] }}</td>
                                        <td>

                                            @php $batchInactive = isset($value->batch->status) && $value->batch->status == 0;   @endphp

                                            @if ( $batchInactive )
                                                <span class="badge bg-danger">Batch Inactive</span>
                                            @endif

                                            @if( !$value->is_discipline_changed )
                                                <a href="{{ $batchInactive? 'javascript:void(0)' : url('my-courses/edit-doctor-course-discipline/'.$value->id) }}"
                                                    target="_blank" class="btn mt-1 btn-sm btn-info {{ $batchInactive? 'disabled' : '' }}">Change {{  $value->course->isCombined() ? ' Faculty ':''  }} Discipline </a>
                                            @endif

                                            <a href="{{ $batchInactive? 'javascript:void(0)' : url('doc-profile/view-course-result/'.$value->id) }}"
                                                target="_blank" class="btn mt-1 btn-sm btn-primary {{ $batchInactive? 'disabled' : '' }}">Result</a>

                                            @if($value->id)
                                                <a href="{{ $batchInactive? 'javascript:void(0)' : url("doctor-course-batch-schedule/".$value->id) }}"
                                                    class='btn mt-1 btn-sm btn-primary {{ $batchInactive? 'disabled' : '' }}' target='_self'>Schedule</a>
                                            @endif
                                            @if(($value->institute_id==6 ||  $value->course->isCombined() )  && $value->candidate_type == '')
                                                <a href="{{ $batchInactive? 'javascript:void(0)' : url('my-courses/edit-doctor-course-candidate/'.$value->id) }}"
                                                    class='btn mt-1 btn-sm btn-info {{ $batchInactive? 'disabled' : '' }}' target='_blank'>Set Candidate Type</a>
                                            @endif
                                            @if(isset($value->batch) && isset($value->batch->system_driven) && ($value->batch->system_driven == "Mandatory" || $value->batch->system_driven == "Optional"))
                                                <span class='btn mt-1 btn-sm btn-info system_drivenn' id="batch_{{$value->batch->id}}" data-batch-id="{{$value->batch->id}}" data-doctorcourse="{{$value->id}}">System Driven</span>
                                            @endif
                                        </td>
                                        <td>{{ $value->candidate_type ?? '' }}</td>
                                        <td>{{ $value['year'] }}</td>
                                        <td>{{ (isset($value->session->name))?$value->session->name:'' }}</td>
                                        <td>{{ (isset($value->course->name))?$value->course->name:'' }}</td>
                                        <td>
                                            @if( $value->course->isCombined() )
                                                <strong><em>Residency Faculty:</em></strong> {{ $value->faculty->name ?? '' }}<br>
                                                <strong><em>Residency Discipline:</em></strong> {{ (isset($value->subject->name))?$value->subject->name:'' }}<br>
                                                <strong><em>FCPS Part-1 Discipline:</em></strong> {{ $value->bcps_subject->name ?? '' }}
                                            @else
                                                {{ (isset($value->subject->name))?$value->subject->name:'' }}<br>
                                            @endif
                                        </td>
                                        <td>{{ (isset($value->batch->name))?$value->batch->name:'' }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        

                </div>

                
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="system_driven" tabindex="-1" role="dialog" aria-labelledby="system_driven_header" aria-hidden="false">
            <div class="modal-dialog system_driven_dialog">
            <div class="modal-content">
                <!-- <div class="modal-header">
                <h5 class="modal-title" id="system_driven_header">System Driven</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="false"></span>
                </button>
                </div>
                <div class="modal-body system_driven_body">
                ...
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div> -->
            </div>
            </div>
        </div>

    </div>

    

</div>


@endsection

@section('js')

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.closed').css("backgorund-color:gray;color:white;font-size:30px;");
            $("body").on("click",".system_drivenn",function(){
                var batch_id = $(this).data('batch-id');
                var doctor_course_id = $("#batch_"+batch_id).data('doctorcourse');
                $('#system_driven .modal-content').load('/system-driven',{batch_id : batch_id,doctor_course_id:doctor_course_id, _token: '{{ csrf_token() }}'},function(){
                    $('#system_driven').modal('show');
                });
            });

            $("body").on("click",".closed",function(){
                $('#system_driven').modal('hide');
            });

            $(document).on('change', '[name="system_driven"]', function(e) {
                e.preventDefault();
                var operation = "";
                if($(this).val() == "Yes") {
                    operation = "insert";      
                }
                else if($(this).val() == "No")
                {
                    operation = "delete";
                }
                var batch_id = $('.doctor-course-id').data('batch-id');
                var doctor_course_id = $('.doctor-course-id').data('doctor-course-id');       
                                
                $.ajax({
                    type: "POST",
                    url: '/add-system-driven',
                    dataType: 'HTML',
                    data: {batch_id : batch_id,doctor_course_id:doctor_course_id, operation : operation, _token: '{{ csrf_token() }}' },
                    success: function( data ) { 
                        var data = JSON.parse(data);
                        
                        if(data['success_status'] == "insert_success")
                        {
                            console.log("Insert Successfulley");
                        }
                        if(data['success_status'] == "delete_success")
                        {
                            console.log("Deleted Successfulley");
                        }
                        if(data['success_status'] == "insert_completed")
                        {
                            if($("[name='system_driven']:checked"))
                            $("[name='system_driven']:checked").removeAttr('checked');
                            alert(data['message']);
                        }
                        $( ".closed" ).prop( "disabled", false );
                                            
                    }
                });                 
            });

        });

            // $('.doctor2').select2({
            //     minimumInputLength: 3,
            //     placeholder: "Please type doctor's name or bmdc no",
            //     escapeMarkup: function (markup) { return markup; },
            //     language: {
            //         noResults: function () {
            //             return "No Doctors found, for add new doctor please <a target='_blank' href='{{ url('admin/doctors/create') }}'>Click here</a>";
            //         }
            //     },
            //     ajax: {
            //         url: '/admin/search-chapter-list',
            //         dataType: 'json',
            //         type: "GET",
            //         quietMillis: 50,
            //         data: function (term) {
            //             return {
            //                 term: term
            //             };
            //         },
            //         processResults: function (data) {
            //             return {
            //                 results: $.map(data, function (item) {
            //                     console.log(item.id);
            //                     $('.select2-selection__rendered').attr('data-id' , item.id);
            //                     return { id:item.id , text: item.name_bmdc };
            //                 })
            //             };
            //         }
            //     }
            // });
    </script>

@endsection