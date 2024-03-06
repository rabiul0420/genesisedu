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
                Doctor ratings
            </li>
        </ul>

    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>Doctor Ratings (Schedule {{ $schedule_detials->count() }} of {{ $schedule_detials->total() }})
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="form-body">
                        <form>

                            <div class="row sc_search">

                                <div class="lecture_video">
                                    <div class="form-group">
                                        <h5>Video Name <span class="text-danger"></span></h5>
                                        <div class="controls">
                                            @php  $lecture_video->prepend('Select Video', ''); @endphp
                                            {!!
                                                Form::select('lecture_video_id',$lecture_video, '' ,
                                                ['class'=>'form-control batch2','id'=>'batch_id'])
                                            !!}
                                            <i></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="mentor">
                                    <div class="form-group">
                                        <h5>Video Name <span class="text-danger"></span></h5>
                                        <div class="controls">
                                            @php  $mentors->prepend('Select Mentor', ''); @endphp
                                            {!!
                                                Form::select('mentor_id',$mentors, '' ,
                                                ['class'=>'form-control','id'=>'mentor_id'])
                                            !!}
                                            <i></i>
                                        </div>
                                    </div>
                                </div>
                                <div style="display: flex; align-items: flex-end; padding-bottom: 15px">
                                    <button type="submit" class="btn btn-success">View Ratings</button>
                                </div>

                            </div>

                        </form>

                        <div style="display: flex; justify-content: end; align-items: center;">
                            {{ $schedule_detials->links() }}
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                @foreach( $schedule_detials as $detail )
                                    <div style="margin-bottom: 24px; border: 1px dashed #999; border-radius: 20px; background: white; padding: 4px 8px; overflow: hidden;">
                                        <div style="display: flex; flex-wrap: wrap; gap: 12px; padding: 12px 0;">
                                            <span style="padding: 8px 12px; background: #ddd; border-radius: 10px;">
                                                <b>Schedule :</b> {{$detail->time_slot->schedule->name ?? ''}}
                                            </span>
                                            <span style="padding: 8px 12px; background: #ddd; border-radius: 10px;">
                                                <b>Lecture Video :</b> {{$detail->video->name ?? ''}}
                                            </span>
                                            <span style="padding: 8px 12px; background: #ddd; border-radius: 10px;">
                                                <b>Mentor :</b> {{ $detail->mentor->name ?? ''}}
                                            </span>
                                        </div>
                                        @include( 'admin.batch_schedules.class-mentor-ratings' )
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div style="display: flex; justify-content: end; align-items: center;">
                            {{ $schedule_detials->links() }}
                        </div>

                    </div>
                </div>
            </div>



        </div>
    </div>

@endsection

@section( 'styles' )
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />

    <style>
        .sc_search {
            display: flex;
            justify-content: center
        }

    </style>

@endsection

@section( 'js' )

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script>
        $('#batch_id').select2({});
    </script>
@endsection
