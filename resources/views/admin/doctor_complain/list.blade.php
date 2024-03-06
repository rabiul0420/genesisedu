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
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Doctor</th>
                            <th>User</th>
                            <th>Reply By</th>
                            <th>Message</th>
                            <th>Doctor Ask</th>
                            <th>Is Read</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($doctor_ask_replies as $doctor_ask_reply)
                            <tr>
                                <td>{{ $doctor_ask_reply->id }}</td>
                                <td>{{ $doctor_ask_reply->doctor->name ?? '' }}</td>
                                <td>{{ $doctor_ask_reply->user->name ?? '' }}</td>
                                <td>{{ $doctor_ask_reply->message_by ? $doctor_ask_reply->message_by : ''  }}</td>
                                <td>{{ $doctor_ask_reply->message }}</td>
                                <td>{{ $doctor_ask_reply->doctor_ask->video->name ?? '' }}</td>
                                <td>{{ $doctor_ask_reply->is_read }}</td>
                                <td>
                                    <!-- @can('Online Exam Links Edit') -->
                                    <a href="{{ url('admin/doctor-ask-reply/'.$doctor_ask_reply->id ) }}" class="btn btn-xs btn-primary">Reply</a>
                                    <!-- <a href="{{ url('admin/doctor-ask-reply/'.$doctor_ask_reply->id.'/edit' ) }}" class="btn btn-xs btn-primary">Edit Reply</a>
                                     -->
                                    <!-- @endcan -->
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
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {
            $('.datatable').DataTable({
                responsive: true,
                "columnDefs": [
                    { "searchable": false, "targets": 7 },
                    { "orderable": false, "targets": 7 }
                ]
            })
        })
    </script>

@endsection