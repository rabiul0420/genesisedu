@extends('admin.layouts.app')
@section('banner-sliders', 'active')
@section('content')

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
                        <i class="fa fa-globe"></i>Banner Slider Show
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body" style="max-width: 560px; margin: 30px auto;">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <tr>
                            <th>Image</th>
                            <td class="text-left" style="max-width: 500px">
                                <img src="{{ asset($bannerSlider->image) }}" style="width: 100%">
                            </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td class="text-left">{{ ($bannerSlider->status==1) ? 'Active' : 'InActive' }}</td>
                        </tr>
                        <tr>
                            <th>Priority</th>
                            <td class="text-left" min="0">{{ $bannerSlider->priority ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>Action</th>
                            <td class="text-left">
                                @can('Banner Slider')
                                <a href="{{ route('banner-sliders.edit', $bannerSlider->id) }}" class="btn btn-xs btn-warning">Edit</a>
                                @endcan
                                @can('Banner Slider')
                                <form action="{{ route('banner-sliders.destroy', $bannerSlider->id) }}" method="POST" style="display: inline">
                                    {{method_field('Delete')}}
                                    {{ csrf_field() }}
                                    <button onclick="return confirm('Are You Sure ?')" class='btn btn-xs btn-danger' type="submit">Delete</button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    </table>
                    <div class="text-center">
                        <a class="btn btn-sm btn-info" href="{{ route('banner-sliders.index') }}">Back to list</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection
