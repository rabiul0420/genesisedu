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
                        @can('Lecture Sheet Aritcle')
                        <a href="{{url('admin/lecture-sheet-article/create')}}"> <i class="fa fa-plus"></i> </a>
                        @endcan
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
                            <th>Title</th>
                            <th>Institute</th>
                            <th>Course</th>
                            <th>Class/Chapter</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($lecture_sheet_articles as $lecture_sheet_article)
                            <tr>
                                <td>{{ $lecture_sheet_article->id }}</td>
                                <td>{{ $lecture_sheet_article->title }}</td>
                                <td>{{ isset($lecture_sheet_article->institute->name)?$lecture_sheet_article->institute->name:'' }}</td>
                                <td>{{ isset($lecture_sheet_article->course->name)?$lecture_sheet_article->course->name:'' }}</td>
                                <td>{{ isset($lecture_sheet_article->topic->name)?$lecture_sheet_article->topic->name:'' }}</td>
                                <td>{{ ($lecture_sheet_article->status==1)?'Active':'InActive' }}</td>
                                <td>
                                    @can('Lecture Sheet Aritcle')
                                    <a href="{{ url('admin/lecture-sheet-article/'.$lecture_sheet_article->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>
                                    @endcan
                                    @can('Lecture Sheet Aritcle')
                                    {!! Form::open(array('route' => array('lecture-sheet-article.destroy', $lecture_sheet_article->id), 'method' => 'delete','style' => 'display:inline')) !!}
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
                    { "searchable": false, "targets": 5 },
                    { "orderable": false, "targets": 5 }
                ]
            })
        })
    </script>

@endsection
