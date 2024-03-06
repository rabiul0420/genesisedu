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
                        <a href="{{url('admin/online-lecture-link/create')}}"> <i class="fa fa-plus"></i> </a>
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
                            <th>Online Lecture</th>
                            <th>Year</th>
                            <th>Session</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Faculty</th>
                            <th>Discipline</th>
                            <th>Batch</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($online_lecture_links as $online_lecture_link)
                            <tr>
                                <td>{{ $online_lecture_link->id }}</td>
                                <td>{{ $online_lecture_link->online_lecture_address->name ?? '' }}</td>
                                <td>{{ $online_lecture_link->year }}</td>
                                <td>{{ $online_lecture_link->session->name ?? '' }}</td>
                                <td>{{ $online_lecture_link->institute->name ?? '' }}</td>
                                <td>{{ $online_lecture_link->course->name ?? '' }}</td>
                                <td>{{ isset($online_lecture_link->faculty->name) ? $online_lecture_link->faculty->name : '' }}</td>
                                <td>{{ isset($online_lecture_link->subject->name) ? $online_lecture_link->subject->name : '' }}</td>
                                <td>{{ $online_lecture_link->batch->name ?? '' }}</td>
                                <td>{{ ($online_lecture_link->status==1)?'Active':'InActive' }}</td>
                                <td>
                                    @can('Online Exam Links Edit')
                                    <a href="{{ url('admin/online-lecture-link/'.$online_lecture_link->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan
                                    @can('Online Exam Links Delete')
                                    {!! Form::open(array('route' => array('online-lecture-link.destroy', $online_lecture_link->id), 'method' => 'delete','style' => 'display:inline')) !!}
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
                    { "searchable": false, "targets": 10 },
                    { "orderable": false, "targets": 10 }
                ]
            })
        })
    </script>

@endsection
