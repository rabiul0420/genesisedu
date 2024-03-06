@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                <a href="{{ url('/admin/topic') }}">topic</a>
                {{-- <i class="fa fa-angle-right"></i><span> {{$action == 'duplicate' ? 'Duplicate':'Edit'}}</span> --}}
                <i class="fa fa-angle-right"></i><li>create</li>
            </li>
        </ul>

    </div>

    @if( Session::has('message') )
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i><span>{{$action == 'duplicate' ? 'Duplicate':'Edit'}} Class/Chapter</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->

                    @if( $action == 'edit')
                        {!! Form::open(['action'=>['Admin\TopicController@update', $topic->id],'method'=>'PUT', 'class'=>'form-horizontal']) !!}
                    @else
                        {!! Form::open(['action'=>['Admin\TopicController@store'],'class'=>'form-horizontal']) !!}
                    @endif
                        <div class="form-body">

                            <div class="form-group">
                                <label class="col-md-3 control-label">Class/Chapter Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        <input type="text" name="name" required value="{{ old('name', $topic->name ?? '') }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    {!! Form::select('year', $years, old( 'year', $topic->year ?? '' ), ['class' => 'form-control year'] ) !!}<i></i>
                                </div>
                            </div>

                            <div class="institutes">
                                @include( 'admin.topic.institutes', [ 'selected_institute_id' => old( 'institute_id', ( $topic->institute_id ?? 0 ) ) ] )
                            </div>

                            <div class="courses">
                                @include( 'admin.topic.courses', [
                                    'selected_course_id' => old( 'course_id', $topic->course_id ?? 0 ),
                                    'institute_id' => old( 'institute_id', $topic->institute_id ?? 0 ),
                                ] )
                            </div> 

                            <div class="sessions">
                                @include( 'admin.topic.sessions', [
                                    'selected_session_id' => old( 'session_id', $topic->session_id ?? 0 ) ,
                                    'course_id' => old( 'course_id', $topic->course_id ?? 0 ) ,
                                    'year' => old( 'year', $topic->year ?? 0 ) ,
                                ])
                            </div>          

                            <div class="faculties-disciplines">
                                @include( 'admin.topic.faculties_disciplines',
                                [
                                    'selected_faculty_ids' => old( 'faculty_ids', $selected_faculty_ids ?? 0),
                                    'selected_subject_ids' => old( 'subject_ids', $selected_subject_ids ?? 0),
                                    'course_id' => old( 'course_id', $topic->course_id ?? 0),
                                    'institute_id' => old( 'institute_id', $topic->institute_id ?? 0 ),

                                ])
                            </div>


                            <div class="form-group">
                                <label class="col-md-3 control-label">Mentor (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        @php $teachers->prepend( 'Select mentor', '' ) @endphp
                                        {!! Form::select( 'teacher_ids[]',$teachers, $selected_teachers ??'', ['class'=>'form-control  select2 ','data-placeholder' => 'Select Faculty','multiple' => 'multiple',] ) !!}<i></i>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn btn-info">{{$action == 'duplicate' ? 'Duplicate':'Submit'}}</button>
                                    <a href="{{ url('admin/topic') }}" class="btn btn-default">Cancel</a>
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

@section( 'styles' )
    <link href="{{ asset('css/select2.css') }}" type="text/css" rel="stylesheet"/>
@endsection
@section( 'js' )
    <script src="{{ asset('js/select2.js') }}" type="text/javascript"></script>

    <script>

        ( function( institute_id, course_id, session_id, selected_subject_ids ){

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                }
            });

            jQuery.prototype.loadView = function ( view_name, data, callback ) {
                if( $(this).length > 0 ) {
                    data.view = view_name;
                    return $.ajax({
                        type: "GET", url: '/admin/load-view', dataType: 'HTML', data,
                        success: ( data, xhr ) => {
                            $(this).html( data );
                            if( typeof callback == 'function') {
                                callback.call( this, xhr );
                            }
                        }
                    });
                }
                return null;
            };

            function onChangeInstitute( ){
                institute_id = $( this ).val( );
                $( '.courses' ).loadView( 'admin.topic.courses', { institute_id });
            }

            function onChangeYear( ){
                year = $( this ).val( );
                course_id = $( '#course_id' ).val( );
                $('.sessions').loadView( 'admin.topic.sessions', { course_id,year } );
                faculty_discipline_manager().load( );
            }


            function onChangeCourse( ){
                course_id = $( this ).val( );
                year = $( '.year' ).val( );
                $('.sessions').loadView( 'admin.topic.sessions', { course_id,year } );
                faculty_discipline_manager().load( );
            }           

            function faculty_discipline_manager( ) {


                function onChangeDiscipline( ){
                    selected_subject_ids = $( this ).val( );
                }

                function onLoadDiscipline( ) {
                    $(this).find( '#subject_id' ).select2();
                    App.handleSelect2AllSelector()
                }

                function onChangeFaculty( ){
                    $('.faculties-disciplines').find( '.faculty-subjects' )
                        .loadView( 'admin.topic.disciplines', {
                            institute_id,
                            course_id,
                            faculty_ids: $(this).val( ),
                            selected_subject_ids
                        }, onLoadDiscipline );

                }

                function onLoadFacultyDiscipline() {
                    App.handleSelect2AllSelector()
                    $(this).find('#faculty_id').select2( );
                    $(this).find('#subject_id').select2( );
                    $(this).find('#bcps_subject_id').select2();
                }

                function load( ){
                    $('.faculties-disciplines').loadView( 'admin.topic.faculties_disciplines', { institute_id, course_id }, onLoadFacultyDiscipline );
                }


                return {
                    onChangeDiscipline, onChangeFaculty, load
                }
            }
            $("body").on( "change", ".year", onChangeYear );
            $("body").on( "change", "#institute_id", onChangeInstitute );
            $("body").on( "change", "#course_id", onChangeCourse );
            $("body").on( "change", "#faculty_id", faculty_discipline_manager().onChangeFaculty );
            $("body").on( "change", "#subject_id", faculty_discipline_manager().onChangeDiscipline );

            $( '.select2' ).select2();
            App.handleSelect2AllSelector();
        }(
            {{ old( 'institute_id', $topic->institute_id ?? 0 )}},
            {{ old( 'course_id', $topic->course_id ?? 0 )}},
            {{ old( 'session_id', $topic->session_id ?? 0 )  }},
            []
        ));

    </script>

@endsection
