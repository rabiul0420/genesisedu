@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'Notice' }}</h2>
                    </div>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                        <div class="col-md-12 py-3">
                            <table class="bg-white table table-striped table-bordered rounded datatable p-1">
                                <thead>
                                <tr>
                                    <th style="width: 50px;">SL</th>
                                    @php $sl = 1; @endphp
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>

                                    @foreach($all_notices as $notice)
                                                                            
                                        <tr>
                                            <td>{{ $sl++ }}</td>
                                            <td>{{ $notice->title }}</td>
                                            <td style="color: #555; font-size: 14px">{{ $notice->created_at->format('d, M Y h:i:s:a') }}</td>
                                            <td>
                                                <a href="{{ url('notice/notice-details/'.$notice->id) }}" class="btn btn-sm {{ in_array($notice->id, $notice_read) ? 'btn-outline-primary' : 'btn-primary' }}">More Details</a>
                                            </td>
                                        </tr>
                                        
                                    @endforeach

                                </tbody>
                            </table>
                        </div>

                </div>
            </div>
        </div>

    </div>


</div>
@endsection
