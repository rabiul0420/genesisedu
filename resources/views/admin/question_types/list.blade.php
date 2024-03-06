@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>{{ $title }}</li>
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
                        <i class="fa fa-globe"></i>{{ $title }}
                        <a href="{{ action('Admin\QuestionTypesController@create') }}"> <i class="fa fa-plus"></i> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>MCQ</th>
                            <th>MCQ Mark</th>
                            <th>MCQ Negative Mark</th>
                            <th>SBA</th>
                            <th>SBA Mark</th>
                            <th>SBA Negative Mark</th>
                            <th>Full Mark</th>
                            <th>Duration ( In Minutes )</th>
                            <th>Paper or Faculty</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($question_types as $question)
                            <tr>
                                <td>{{ $question->id }}</td>
                                <td>{{ $question->title }}</td>
                                <td>{{ $question->mcq_number }}</td>
                                <td>{{ $question->mcq_mark }}</td>
                                <td>{{ $question->mcq_negative_mark }}</td>
                                <td>{{ $question->sba_number }}</td>
                                <td>{{ $question->sba_mark }}</td>
                                <td>{{ $question->sba_negative_mark }}</td>
                                <td>{{ $question->full_mark }}</td>
                                <td>{{ $question->duration/60 }}</td>
                                <td>{{ $question->paper_faculty }}</td>
                                <td>
                                
                                    <a href="{{ url('admin/question-types/'.$question->id.'/edit') }}" class="btn btn-xs btn-primary">Edit</a>

                                    {!! Form::open(array('route' => array('question-types.destroy', $question->id), 'method' => 'delete','style' => 'display:inline')) !!}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                    {!! Form::close() !!}

                                    
                                
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
                "ordering": false,
                "columnDefs": [
                    { "searchable": false, "targets": 3 },
                    { "orderable": false, "targets": 3 }
                ]
            })
        })
    </script>

@endsection
