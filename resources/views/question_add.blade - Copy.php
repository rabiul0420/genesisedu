@extends('layouts.app')

@section('content')
<div class="container">


    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color:#7fc9f6; color: #FFFFFF;"><h3>Add Question</h3></div>

                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                        

                        <div class="col-md-12">

                            <div class="portlet-body form">
                                
                                {!! Form::open(['url'=>['question-submit'],'method'=>'post','files'=>true,'class'=>'form-horizontal']) !!}


                                <div class="form-body">
                                    
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="input-icon right">
                                                BMDC No. : <br>
                                                <input type="text" name="bmdc_no" value="{{ $doc_info->bmdc_no }}" class="form-control" required readonly="readonly">
                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="input-icon right">
                                                Select Course : <br>
                                                @php  $courses->prepend('Select Course', ''); @endphp
                                                {!! Form::select('course_id',$courses, old('course_id')?old('course_id'):'' ,['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}
                                            </div>
                                        </div>
                                    </div>
                                
                                    <div class="batch">

                                    </div>

                                    <div class="lecture_video">

                                    </div>

                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-0 col-md-9">
                                                <button type="submit" class="btn btn-info">Submit</button>
                                            </div>
                                        </div>
                                    </div>

                                    
                            {!! Form::close() !!}

                            
                            </div>

                        </div>



                </div>
            </div>
        </div>

    </div>

</div>
@endsection


@section('js')

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    

    <script type="text/javascript">
        $(document).ready(function() {

                      
            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/course-batch',
                    dataType: 'HTML',
                    data: {course_id : course_id},
                    success: function( data ) {
                        $('.batch').html(data);
                        $('.lecture_video').html('');
                    }
                });
            })
            

            $("body").on( "change", "[name='batch_id']", function() {
                var batch_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/batch-lecture-video',
                    dataType: 'HTML',
                    data: {batch_id : batch_id},
                    success: function( data ) {
                        $('.lecture_video').html(data);
                    }
                });
            })

            
            // $("body").on( "change", "[name='lecture_video_id']", function() {
            //     window.location.href = "question-answer/"+$(this).val();
            // })


        })
    </script>
    

@endsection