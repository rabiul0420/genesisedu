@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a> <i class="fa fa-angle-right"></i>
            </li>
            <li><a href="{{ url('admin/executive') }}">Executive List</a></li>
            <i class="fa fa-angle-right"></i><li>Executive Edit</li>
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
                        <i class="fa fa-reorder"></i>Executive Edit
                    </div>
                </div>

                <div class="portlet-body form">
                    {!! Form::open(['action'=>['Admin\executiveController@update',$executive->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                        <div class="form-body">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        <input type="text" name="name" required value="{{ $executive->name }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Mobile No (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        <input type="text" name="mobile" required value="{{ old('mobile', $executive->mobile) }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Email (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        <input type="text" name="email" required value="{{ $executive->email }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">User Select </label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        {{-- {!! Form::select('course_id',$courses,isset($executive_course->course->id) ? $executive_course->course->id : ' ' , [ 'class'=>'form-control ' ]) !!} --}}
                                        <select name="user_id" class="form-control course">
                                            <option value=" ">---Select Course---</option>
                                            @foreach ($users as $key=>$user)
                                                <option value="{{ $key }}" {{  $executive->user_id ==  $key ? 'selected' : ' ' }} >{{ $user }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Status</label>
                                <div class="col-md-4">
                                    <div class="input-icon right">
                                        {!! Form::select('status',['Inactive','Active'], old('status', $executive->status),[ 'class'=>'form-control' ]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" class="btn btn-info">Update</button>
                                    <a href="{{ url('admin/executive/executiveList') }}" class="btn btn-default">Cancel</a>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script type="text/javascript">
       $(document).ready(function(){
           $('.course').select2({});
       })
    </script>
@endsection