@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">Pending Lecture Video</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif

                    <div class="col-md-12">
                        <div class="portlet">
                            <div class="portlet-body py-1">
                                <div class="row mx-0">
                                    @foreach ($doctor_courses as $doctor_course)
                                    <div class="col-md-6 p-1">                                                                           
                                        <a href="{{ route('request-lecture-video.show', $doctor_course->id) }}" class="w-100 h-100 bg-white px-3 py-4 border rounded-lg" style="display: flex; flex-direction: column;">
                                            <h6 class="text-secondary pb-1">{{  $doctor_course->course->name ?? '' }}</h6>
                                            <h5 class="text-primary">{{  $doctor_course->batch->name ?? '' }}</h5>
                                        </a>
                                    </div>
                                    @endforeach                                            
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>


</div>
@endsection
