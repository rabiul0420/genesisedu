@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>Administrator Edit</li>
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
                        <i class="fa fa-reorder"></i>Mentor Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                        {!! Form::open(['action'=>['Admin\MentorController@update',$user->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                        <div class="form-body">

                            <div id="mentor_question_data" {!! $user->isMentor() ? '':'style="display: none"' !!}>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Question Subjects</label>
                                    <div class="col-md-4">
                                    {!! Form::select('subject_id[]', $question_subjects, old('question_subjects') ? old( 'question_subjects' )
                                        : $selected_subjects, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'subject_id']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        <label><input type="checkbox" class="select-all" data-target="#subject_id"> Select All </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Question Chapters</label>
                                    <div class="col-md-4" id="question_chapters">
                                        {!! Form::select('chapter_id[]', $question_chapters, old('chapter_id') ? old( 'chapter_id' )
                                            : $selected_chapters, ['class' => 'form-control select2', 'multiple' => 'multiple', 'id' => 'chapter_id' ]) !!}
                                    </div>
                                    <div class="col-md-2">
                                        <label><input type="checkbox" class="select-all" data-target="#chapter_id"> Select All </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Question Topics</label>
                                    <div class="col-md-4" id="question_topics">
                                        {!! Form::select( 'topic_id[]', $question_topics, old('topic_id') ? old( 'topic_id' )
                                            : $selected_topics, ['class' => 'form-control select2', 'id' => 'topic_id','multiple' => 'multiple' ]) !!}
                                    </div>
                                    <div class="col-md-2">
                                        <label><input type="checkbox" class="select-all" data-target="#topic_id"> Select All </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Exams</label>
                                    <div class="col-md-4">
                                        {!! Form::select( 'exam_ids[]', $exams, old('exam_ids') ? old( 'exam_ids' )
                                            : $selected_exam_ids, ['class' => 'form-control select2', 'id' => 'exam_id', 'multiple' => 'multiple' ]) !!}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-1 control-label">Access Upto</label>
                                    <div class="col-md-4">
                                        <input class="form-control" type="date" name="access_upto" value="{{ $access_upto }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-9">
                                    <button type="submit" class="btn btn-info">Submit</button>
                                    <a href="{{ url('admin/administrator') }}" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </div>
                       {!! Form::close() !!}
                    <!-- END FORM-->
                </div>
            </div>

        </div>
    </div>

@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script type="text/javascript">
        // DO NOT REMOVE : GLOBAL FUNCTIONS!
        $(document).ready(function() {

            $('.select2').select2();

            function isMentor( roles ){
                if( !roles )
                    return false;

                if( roles.indexOf('Administrator') > -1 || roles.indexOf('Super Admin') > -1 ) {
                    return false;
                }

                return roles.indexOf( 'Mentor' ) > -1;
            }


            $('#user_role').on( 'change', function () {
                var roles = $(this).val();

              console.log( 'is Mentor ', isMentor( roles ) );

                if( isMentor( roles ) ) {
                    $('#mentor_question_data').show();
                    $('#subject_id').select2();
                    $('#chapter_id').select2();
                    $('#topic_id').select2();
                } else {
                    $('#mentor_question_data').hide();
                }
            });


            $("body").on( "change", "#subject_id", function() {
                var subject_id = $(this).val();

                console.log( $( '#chapter_id' ).val( ) );

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/ajax-question-chapter?multiple=true&selection_only=true&required=false',
                    dataType: 'HTML',
                    data: { subject_id: subject_id, selected: $( '#chapter_id' ).val( )  },
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('#question_chapters').html('');
                        // $('.topic').html('');
                        $('#question_chapters').html( data['chapters'] );
                        $('#chapter_id').select2();
                    }
                });
            })

            $("body").on( "change", "#chapter_id", function() {
                var subject_id = $("#subject_id").val();
                var chapter_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/ajax-question-topic?multiple=true&selection_only=true&required=false',
                    dataType: 'HTML',
                    data: { subject_id:subject_id, chapter_id: chapter_id,selected: $( '#topic_id' ).val( ) },
                    success: function( data ) {
                        var data = JSON.parse(data);

                        $( '#question_topics' ).html('');
                        $( '#question_topics' ).html(data['topics']);
                        $( '#topic_id' ).select2();
                    }
                });
            })


            $('.select-all').each( function () {

                $(this).on('change', function () {
                    const target = $( this ).data('target');

                    if( $(this).prop( "checked" ) ) {
                        $(target).find( "option" ).prop( "selected", "selected" );
                        $(target).trigger( "change" );
                    }else {
                        $(target).find( "option" ).removeAttr( "selected"  );
                        $(target).trigger( "change" );
                    }
                })
            });

            // $("body").on( "click", "#checkbox", function() {
            //     if($("#checkbox").is(':checked') ){
            //         $(".disciplines .select2 > option").prop("selected","selected");
            //         $(".disciplines .select2").trigger("change");
            //     }else{
            //         $(".disciplines .select2 > option").removeAttr("selected");
            //         $(".disciplines .select2").trigger("change");
            //     }
            // });

        })
    </script>




@endsection
