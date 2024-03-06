@extends('special-group.layout')

@section('heading', 'Your Special Groups');

@section('section-content')



    <div class="container-fluid">

        <div class="row mx-0">
            @foreach( $groups as $group )
                <div class="col-md-6">
                    <a title="{{ $group->name }}"
                            style="background: gold;color: #a88f00;"
                           class="w-100 col-md-12 px-3 py-4 my-1 border bg rounded-lg shadow-sm"
                           href="{{   route( 'doctor-group.exams', [ 'group_id' => $group->id ] ) }}">
                        <h6>{{ $group->name }}</h6>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

@endsection
