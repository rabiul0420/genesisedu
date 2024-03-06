@extends('layouts.app')
@section('content')
<section id="course_part">
    <div class="container px-lg-0 pb-lg-3 pb-2">
        <div class="row">
            <div class="col text-center">
                <h2>Our Courses</h2>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 g-2 g-lg-3">
            @php $i=1; @endphp
            @foreach($courses as $course)
            <div class="col item item-{{$i++}}">
                <a href="{{url('course-detail/'.$course->id)}}" class="card">
                    <div class="card-body">
                        <div class="w-100 heading_box_height">
                            <h5>{{$course->name}}</h5>
                        </div>
                        <div class="w-100">
                            <p>Learn More<i class="fa fa-arrow-circle-right"></i></p>
                        </div>
                    </div>
                </a>
            </div>
            @php if($i==5){$i=1;}; @endphp
            @endforeach
        </div>
    </div>
</section>
@endsection