@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a></i>
            </li>
            <?php
            $urls='';
            foreach($breadcrumb as $key=>$value){ $urls .= $value.'/';
                echo '<li> <i class="fa fa-angle-right"></i> <a href="'.url('/').substr_replace($urls, "", -1).'"> '.$value.' </a></li>';
            }
            ?>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i><?php echo $module_name;?> Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\LectureSheetArticleController@update',$lecture_sheet_article->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                    <div class="form-group">
                            <label class="col-md-3 control-label">Title (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <input type="text" name="title" required value="{{ $lecture_sheet_article->title }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Description (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <textarea id="description" name="description" required class="form-control">{{ $lecture_sheet_article->description  }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="institutes">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php  $institutes->prepend('Select Institute', ''); @endphp
                                        {!! Form::select('institute_id',$institutes, $lecture_sheet_article->institute_id ? $lecture_sheet_article->institute_id :'' ,['class'=>'form-control','required'=>'required','id'=>'institute_id']) !!}<i></i>

                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="courses">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    @php  $courses->prepend('Select Course', ''); @endphp
                                    {!! Form::select('course_id',$courses, isset($lecture_sheet_article->course_id) ? $lecture_sheet_article->course_id : '',['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>
                                </div>
                                <input type="hidden" name="url" value="{{$url}}">
                            </div>
                        </div>


                        <!-- <div class="faculties">
                            @if($institute_type==1)
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Faculty </label>
                                    <div class="col-md-3">
                                        @php  $faculties->prepend('Select Faculty', ''); @endphp
                                        {!! Form::select('faculty_id',$faculties, isset($lecture_sheet_article->faculty_id) ? $lecture_sheet_article->faculty_id : '' ,['class'=>'form-control','id'=>'faculty_id']) !!}<i></i>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="subjects">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Discipline</label>
                                <div class="col-md-3">
                                    @php  $subjects->prepend('Select Discipline', ''); @endphp
                                    {!! Form::select('subject_id',$subjects, isset($lecture_sheet_article->subject_id) ? $lecture_sheet_article->subject_id : '' ,['class'=>'form-control','id'=>'subject_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        <div class="batches">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    @php  $batches->prepend('Select Batch', ''); @endphp
                                    {!! Form::select('batch_id',$batches, isset($lecture_sheet_article->batch_id) ? $lecture_sheet_article->batch_id : '' ,['class'=>'form-control','required'=>'required','id'=>'batch_id']) !!}<i></i>
                                </div>
                            </div>
                        </div> -->
                        <div class="topics">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Class/Chapter (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    @php  $topics->prepend('Select Class/Chapter', ''); @endphp
                                    {!! Form::select('topic_id',$topics, isset($lecture_sheet_article->topic_id) ? $lecture_sheet_article->topic_id : '' ,['class'=>'form-control','required'=>'required','id'=>'topic_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Select Status  (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                        <div class="col-md-3">
                            {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control']) !!}<i></i>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">{{ $submit_value }}</button>
                                <a href="{{ url('admin/lecture-sheet-article') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->
                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->



        </div>
    </div>
    <!-- END PAGE CONTENT-->


@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {

            CKEDITOR.replace( 'description' );

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-courses',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.courses').html('');
                        $('.topics').html('');
                        $('.courses').html(data);
                    }
                });
            })

            $("body").on( "change", "[name='course_id']", function() {
                var institute_id = $("[name='institute_id']").val();
                var course_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-topic',
                    dataType: 'HTML',
                    data: {institute_id:institute_id,course_id: course_id},
                    success: function( data ) {
                        $('.topics').html('');
                        $('.topics').html(data);

                    }
                });
            })


        })
    </script>


@endsection
