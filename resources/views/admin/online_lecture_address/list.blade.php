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
                        <a href="{{url('admin/online-lecture-address/create')}}"> <i class="fa fa-plus"></i> </a>
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
                            <th>Name</th>
                            <th>Lecture Address</th>

                            <th>Password</th>

                            <th>PDF File</th>
                            <th>Downloads</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($online_lecture_addresses as $online_lecture_address)
                            <tr>
                                <td>{{ $online_lecture_address->id }}</td>
                                <td>{{ $online_lecture_address->name }}</td>
                                <td>{{ $online_lecture_address->lecture_address }}</td>

                                <td>{{ $online_lecture_address->password }}</td>

                                <td><a target="_blank" href="http://127.0.0.1:8000/pdf/{{$online_lecture_address->pdf_file}}">{{ $online_lecture_address->pdf_file }}</a></td>
                                <td><a href="{{ url('admin/download-lecture-related-emails/'.$online_lecture_address->id) }}" class="btn btn-xs btn-primary" target="_blank">Download Doctors Emails</a></td>
                                <td>{{ ($online_lecture_address->status==1)?'Active':'InActive' }}</td>
                                <td>
                                    @can('Exam Common Code Edit')
                                    <a href="{{ url('admin/online-lecture-address/'.$online_lecture_address->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan
                                    @can('Exam Common Code Delete')
                                    {!! Form::open(array('route' => array('online-lecture-address.destroy', $online_lecture_address->id), 'method' => 'delete','style' => 'display:inline')) !!}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    {!! Form::close() !!}
                                    @endcan
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
                    { "searchable": false, "targets": 4 },
                    { "orderable": false, "targets": 4 }
                ]
            })
        })
    </script>

@endsection
