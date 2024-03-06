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
                Available Batches Edit
            </li>
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
                        <i class="fa fa-reorder"></i>Available Batches Edit
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->

                    <!-- BEGIN FORM-->
                        @if( $action ?? '' == 'duplicate'  )
                            {!! Form::open(['action'=>'Admin\AvailableBatchesController@store','files'=>true,'class'=>'form-horizontal']) !!}
                        @else
                            {!! Form::open([
                                'action'=>['Admin\AvailableBatchesController@update',$available_batches->id],
                                'method'=>'PUT','files'=>true,'class'=>'form-horizontal'])
                            !!}
                        @endif

                        <div class="form-body">

                            <div class="form-group">
                                <label class="col-md-2 control-label">Course Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" name="course_name" placeholder="Course Name" value="{{ $available_batches->course_name ?? '' }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Batch Page Tab Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <select class="form-control" name="course_type">
                                            <option value="">Select Tab Name</option>
                                            @foreach(App\AvailableBatches::getCourseNames() as $courseType => $courseName)
                                            <option value="{{ $courseType }}" {{ $available_batches->course_type == $courseType ? 'selected' : '' }}>{{ $courseName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Displaying Batch Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" name="batch_name" placeholder="Batch Name" value="{{ $available_batches->batch_name ?? '' }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-md-2 control-label">Displaying Batch Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        {!! Form::select( 'year', $years, old( 'year', $available_batches->batch->year ?? date('Y')), ['class' => 'form-control', 'id' => 'year', 'required' => 'required' ] ) !!}
                                    </div>
                                </div>
                            </div>

                            <div id="institutes">
                                {!! $institutes_view ?? '' !!}
                            </div>

                            <div id="courses">
                                {!! $courses_view ?? '' !!}
                            </div>

                            <div id="sessions">
                                {!! $sessions_view ?? '' !!}
                            </div>

                            <div id="batches">
                                {!! $batches_view ?? '' !!}
                            </div>


                            <div class="form-group">
                                <label class="col-md-2 control-label">Staring Date (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="date" name="start_date" placeholder="Staring Date" value="{{ $available_batches->start_date ?? '' }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Class Days (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" name="days" placeholder="e.g. : Sat, Mon" value="{{ $available_batches->days ?? '' }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Class Time (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                <div class="col-md-3">
                                    <div class="input-icon right">
                                        <input type="text" name="time" placeholder="e.g. : 2:00pm - 4:00pm" value="{{ $available_batches->time ?? '' }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Batch Banner Photo (<i class="fa fa-asterisk ipd-star"
                                        style="font-size:9px;"></i>) </label>
                                <div class="col-md-6">
                                    <div class="input-icon right">
                                        <input type="url" name="meta_banner" placeholder="Batch Banner Photo URL / Link"
                                            value="{{ $available_batches->meta_banner ?? '' }}" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Batch Details 
                                (<i class="fa fa-asterisk ipd-star"style="font-size:9px;"></i>) </label>
                                <div class="col-md-6">
                                <div class="input-icon right">
                                <textarea id="details" name="details" >{{ $available_batches->details ?? '' }}</textarea>
                                </div>
                                </div>
                            </div>

                            @include('admin.available_batches.links')


                            <div class="form-group">
                                <label class="col-md-1 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-3">
                                    {!! Form::select('status', ['1' => 'Active','0' => 'InActive'],$available_batches->status,['class'=>'form-control','required'=>'required']) !!}<i></i>
                             </div> 
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-1 col-md-9">
                                    <button type="submit" class="btn btn-info">Update</button>
                                    <a href="{{ url('admin/available-batches') }}" class="btn btn-default">Cancel</a>
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
    @include( 'admin.available_batches.scripts' )
@endsection