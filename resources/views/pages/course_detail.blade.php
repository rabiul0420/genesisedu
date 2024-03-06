@extends('layouts.app')
@section('content')
    <!-- ================= Course Part Start ================= -->
    <section class="course_details mt-5">
        <div class="container px-lg-0">
            <div class="row">
                <div class="col">
                    @foreach($courses as $course)
                    <div class="text-center">
                        <h2 class="main_heading text-center mx-auto">{{$course->name}}</h2>
                    </div>
                    <div class="course_detail">
                        {!!$course->course_detail!!}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!-- ================= Course Part End ================= -->
@endsection