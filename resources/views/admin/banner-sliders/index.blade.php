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
                        <i class="fa fa-globe"></i>Banner Slider List
                        @can('Lecture Video')
                        <a href="{{ route('banner-sliders.create') }}"> <i class="fa fa-plus"></i> </a>
                        @endcan
                    </div>
                </div>
                <div>
                    <div class="caption">

                    </div>
                </div>
                <div class="portlet-body">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Priority</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($bannerSliders as $bannerSlider)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td width="40%">
                                    <img style="width: 95%; border: 1px solid #707070" src="{{ asset($bannerSlider->image ?? '') }}" alt="">
                                </td>
                                <td>{{ ($bannerSlider->status==1) ? 'Active' : 'InActive' }}</td>
                                <td>{{ $bannerSlider->priority ?? 0 }}</td>
                                <td>
                                    @can('Banner Slider')
                                    <a href="{{ route('banner-sliders.show', $bannerSlider->id) }}" class="btn btn-xs btn-info">Show</a>
                                    @endcan
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
            })
        })
    </script>

@endsection
