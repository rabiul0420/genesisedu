@extends('layouts.app')
@section('content')


<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }
</style>



<!-- ================= Banner Part Start ================= -->
<section id="banner_part">
    <div class="container px-sm-0">
        <div class="row align-items-center gy-4">
            <div class="col-xl-8 col-lg-7 order-lg-0 order-1">
                <div class="box shadow rounded-lg h-100">
                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">

                        <div class="carousel-inner rounded-lg">
                            @foreach ($bannerSliders as $bannerSlider)
                            <div class="carousel-item @if($loop->first) active @endif">
                                <img src="{{ asset($bannerSlider->image)}}" class="d-block w-100">
                            </div>
                            @endforeach
                        </div>

                        <a class="carousel-control-prev" href="#carouselExampleControls" role="button"
                            data-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next" href="#carouselExampleControls" role="button"
                            data-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-5 order-lg-1 order-0">
                @guest('doctor')

                @if (session('message'))
                <div class="alert {{ session('alert-class') }} py-2 mt-3">
                    {{ session('message') }}
                </div>
                @endif
                
                <div id="log_part">
                    <div id="mobile" class="form-box mobile">
                        <div class="button-box">
                            <div id="btn"></div>
                            <button type="button" class="toggle-btn" onclick="login()">Log In</button>
                            <button type="button" class="toggle-btn" onclick="register()">Register</button>
                        </div>
                        <form method="POST" id="login" class="input-group" action="{{ route('login') }}">
                            {{ csrf_field() }}

                            <div class="position-relative w-100 prefix">
                                <span class="position-absolute border-bottom text-dark border-info bg-warning" style="padding:15px 20px; left: 0px;">+88</span>
                            </div>
                                <input type="number" class="input-field ml-5 input_field" style="padding-left:29px;" id="login-email"
                                value="{{ old('bmdc_no') }}" class="form-control" name="mobile_number" placeholder="Mobile Number"
                               required>
                            
                            @if ($errors->has('email'))
                            <span class="help-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif


                            <input type="password" class="input-field password-field" name="password"
                                placeholder="Password" required>
                            @if ($errors->has('password'))
                            <span class="help-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                            @endif
                            <i toggle=".password-field" class="fa fa-fw fa-eye field-icon toggle-password"></i>
                            <input id="remember" type="checkbox" class="check-box">
                            <label class="remember_label" for="remember" {{ old('remember') ? 'checked' : '' }}>Remember
                                Me</label>

                            

                            <button type="submit" class="submit-btn">Log In</button>
                            <a href="{{url('/registration-status')}}" class="forgot_pass forgot-text btn-link">Check Registration Status</a>

                            <a href="{{url('/password-send')}}" class="forgot_pass forgot-text btn-link">Forgot password?</a>
                            <a class="mx-auto btn btn-sm btn-warning rounded-pill" href="{{ route('home') }}" >Alternative Login</a>
                        </form>


                        <form method="POST" action="{{ route('register-post') }}" id="register" class="input-group"
                            enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="text" id="name"
                                class="input-field {{ $errors->has('name') ? ' has-error' : '' }}"
                                placeholder="Full Name" name="name" value="{{ old('name') }}" required>

                            <input type="email" id="register-email"
                                class="input-field {{ $errors->has('email') ? ' has-error' : '' }}" placeholder="E-mail"
                                name="email" value="{{ old('email') }}" required>

                            <input type="text" id="mobile_number"
                                class="input-field {{ $errors->has('mobile_number') ? ' has-error' : '' }}"
                                placeholder="Mobile Number" name="mobile_number" value="{{ old('mobile_number') }}"
                                required>

                            @php $medical_colleges->prepend('Select Medical College', ''); @endphp
                            {!! Form::select('medical_college_id',$medical_colleges, ''
                            ,['class'=>'medical_college select2 mt-2','required'=>'required', 'id'=>'medical_college_id'])
                            !!}

                            <div class="position-relative w-100">
                                <span class="position-absolute border-bottom text-white border-info bg-warning"
                                    style="margin-top: 1px; padding: 12px 15px 7px; left: 0px;">A</span>
                            </div>

                            <input type="number" id="bmdc_no"
                                class="input-field {{ $errors->has('bmdc_no') ? ' has-error' : '' }}"
                                style="padding-left: 12px; margin-left: 40px;" placeholder="BMDC Number" name="bmdc_no"
                                value="{{ old('bmdc_no') }}" data-bs-toggle="tooltip" data-bs-placement="top" 
                                title="1. রেজিস্ট্রেশন করার সময় অবশ্যই আপনার BMDC নাম্বার দিবেন। 

                                2. যারা স্থায়ী BMDC নাম্বার পান নি তারা অস্থায়ী BMDC নাম্বার ব্যবহার করবেন। 
                                
                                3. আপনার অস্থায়ী বিএমডিসি নাম্বারটি হয়তোবা কারো স্থায়ী BMDC নাম্বারের সাথে মিলে যেতে পারে এবং এক্ষেত্রে আপনার অস্থায়ী BMDC নাম্বারটি ওয়েবসাইটে গ্রহণযোগ্য হবে না। তাই এক্ষেত্রে আপনার মোবাইল নাম্বারের শেষ 6-digit ব্যবহার করুন।
                                
                                4. অস্থায়ী BMDC নাম্বার/মোবাইল নাম্বারের শেষ 6-ডিজিট ব্যবহার করা হলে পরবর্তীতে যখন স্থায়ী বিএমডিসি নাম্বার পাবেন সেটা Profile এডিট করে আপনার BMDC number আপডেট করবেন।
                                
                                6. BMDC নাম্বারটি আপনার ইউজার আইডি হিসেবে ব্যবহৃত হবে।"
                                autocomplete="off" required>


                            <input type="password" id="password"
                                class="input-field {{ $errors->has('password') ? ' has-error' : '' }} password-field-r"
                                placeholder="Password" name="password" required>

                            <i toggle=".password-field-r" class="fa fa-fw fa-eye field-icon toggle-password-r"></i>
                            <input id="password-confirm" type="password" class="input-field password-field-r"
                                placeholder="Confirm Password" name="password_confirmation" required>

                            {{-- <label for="photo">Doctor Photo</label>         --}}
                            {{-- <input class="input-field" type="file" id="photo" name="photo" value="" required> --}}

                            <button type="submit" class="submit-btn">Register</button>
                        </form>
                    </div>
                </div>



                <div class="d-flex flex-column-reverse bd-highlight">
                    <div class="p-2 bd-highlight">
                        <a href="https://www.genesisbcscare.com" target="_blank" class="btn btn-info"
                            style="float: center;">BCS</a> {{--  GenesisBCSCare.com --}}

                        <a href="https://www.genesispg.info" target="_blank" class="btn btn-info"
                            style="float: center;"> FCPS-P-II</a> {{--  GenesisBCSCare.com --}}
                    </div>
                </div>


                @else
                <div id="log_part">
                    <div id="mobile" class="form-box" style="background:#ddd">

                        <div class="d-flex justify-content-between profile-mk">
                            <h3 class="d-flex flex-row bd-highlight mb-3">Notice Board</h3>
                            <div class="nav-item d-none active-on">
                                @if(Auth::check())
                                <a class="nav-link menu_button" href="{{url('dashboard')}}">Profile</a>
                                @endif
                            </div>
                        </div>


                        <div class="border-top border-dark mt-1 pb-3"></div>
                        <style>
                            .modal-body p {
                                padding-bottom: 15px;
                            }
                        </style>
                        @foreach ($noticeBoards as $noticeBoard)
                        <!-- Button trigger modal -->
                        <a style="cursor: pointer; position: relative;"
                            class="overflow-hidden d-block text-left text-dark border shadow-sm bg-light rounded-lg px-2 py-3 mx-2 mb-1"
                            data-toggle="modal" data-target="#exampleModal{{ $loop->index }}">
                            {{ $noticeBoard->title }}
                            <span
                                style="position: absolute; right:00; bottom:00; font-size: 12px; padding:3px; color:#535353;">
                                {{ $noticeBoard->created_at->diffForHumans() ?? '' }}
                            </span>
                        </a>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal{{ $loop->index }}" tabindex="-1"
                            aria-labelledby="exampleModalLabel{{ $loop->index }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content text-left">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel{{ $loop->index }}">
                                            {{ $noticeBoard->title }}
                                        </h5>
                                        <span type="button" class="btn-close px-2 ml-5" data-dismiss="modal"
                                            aria-label="Close">X</span>
                                    </div>
                                    <div class="modal-body">
                                        {!! $noticeBoard->description !!}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                @endguest
            </div>
        </div>
    </div>
</section>

<!-- ================= Banner Part End ================= -->

<!-- ================= Doctor of the day Part Start ================= -->
{{--<section id="doctor_of_the_day" style="@forelse($exams as $nothing)@empty display:none !important; @endforelse background-image: url({{ asset('images/doctor_of_the_day.png') }})
">
<div class="py-4">
    <h2 class="heading">DOCTOR OF THE DAY</h2>
    <div class="doctor_day_slider row justify-content-center">
        @foreach ($exams as $k => $row)
        @if(!$row->info)
        @continue
        @endif
        <div class="col px-1">
            <div class="body" style="heightd: 400px;">

                <div class="img mt-2 mb-4">
                    @php $row->info->doctor_course->doctor->photo ??'' ? $photo =
                    $row->info->doctor_course->doctor->photo : $photo="logo.png"; @endphp
                    <img class="img-fluid" src="{{ $photo }}" alt="profile">
                </div>
                <h3 class="name w-100 py-2 text-center" title="{{ $row->info->doctor_course->doctor->name ??'' }}">
                    {{ strlen($row->info->doctor_course->doctor->name ??'')<18 ? $row->info->doctor_course->doctor->name ??'' : substr($row->info->doctor_course->doctor->name ??'', 0, 17).'..' }}
                </h3>

                --}}{{-- <p class="bmdc" title="{{ $row->info->doctor_course->doctor->bmdc_no ??'' }}">
                BMDC : <span>{{ $row->info->doctor_course->doctor->bmdc_no ??'' }}</span>
                </p>
                --}}{{--
                    --}}{{-- <p class="medical_college" title="{{ $row->info->doctor_course->doctor->medicalcolleges->name ??'' }}">
                {{ strlen($row->info->doctor_course->doctor->medicalcolleges->name ??'')<25 ? $row->info->doctor_course->doctor->medicalcolleges->name ??'' : substr($row->info->doctor_course->doctor->medicalcolleges->name ??'', 0, 24).'..' }}
                </p> --}}{{--

                    <p class="batch" title="{{ $row->info->batch->name ??'' }}">
                Batch :
                <span>
                    {{ strlen($row->info->batch->name ??'')<20 ? $row->info->batch->name ??'' : substr($row->info->batch->name ??'', 0, 19).'..' }}
                </span>
                </p>
                <p class="exam" title="{{ $row->info->exam->name ??'' }}">
                    Exam :
                    <span>
                        {{ strlen($row->info->exam->name ??'')<20 ? $row->info->exam->name ??'' : substr($row->info->exam->name ??'', 0, 19).'..' }}
                    </span>
                </p>

                <ul class="results mt-2">
                    <li class="position">
                        <ul>
                            <li>1st</li>
                            <li><span>Position</span></li>
                        </ul>
                    </li>
                    <li class="total_mark">
                        <ul>
                            <li>{{ $row->info->obtained_mark ??'' }}</li>
                            <li><span>Total Marks</span></li>
                        </ul>
                    </li>
                    <li class="negative_mark">
                        <ul>
                            <li>{{ $row->info->negative_mark ??'' }}</li>
                            <li><span>Negative Marks</span></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        @endforeach
    </div>
</div>
</section>--}}

<!-- ================= Doctor of the day Part End ================= -->

<!-- ================= Course Part Start ================= -->
<section id="course_part">
    <div class="container px-lg-0">
        <div class="row">
            <div class="col text-center">
                <h2>Our Courses</h2>
            </div>
        </div>

        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 g-2 g-lg-3">

            @php $i=1; @endphp
            @foreach($courses as $course)
            @if($loop->index==6)
            @break
            @endif
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


        <div class="row">
            <div class="col text-center py-4">
                <a href="{{url('course')}}" class="btn btn-success rounded-lg">More Courses</a>
            </div>
        </div>
    </div>
</section>

<!-- ================= Course Part End ================= -->



<!-- ================= Advertisement Part Start ================= -->
<section id="advertisement_part">
    <div class="">
        <div class="row add_slider">
            @foreach($advertisements as $advertisement)
            <div class="col p-2">
                <img class="img-fluid w-100" src="{{asset($advertisement['image'])}}" alt=""
                    style="height: 180px; object-fit: cover;">
            </div>
            @endforeach
        </div>
    </div>
    @if(!empty(json_decode($advertisements)))
    <hr class="text-white" />
    @endif
    <div class="container px-lg-0">
        <div class="row">
            <div class="col">
                <div class="visitor_counter text-center">

                    <p>Visitors of Last Day <span class="counter">
                            {{ date("D")=='Sat'?41332:'' }}
                            {{ date("D")=='Sun'?42903:'' }}
                            {{ date("D")=='Mon'?46841:'' }}
                            {{ date("D")=='Tue'?43689:'' }}
                            {{ date("D")=='Wed'?45411:'' }}
                            {{ date("D")=='Thu'?42615:'' }}
                            {{ date("D")=='Fri'?51523:'' }}

                        </span></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= Advertisement Part End ================= -->



<!-- ================= Batch Part Start ================= -->


<section id="batch_part">
    <div class="container px-sm-0">
        <div class="big-demo" data-js="hero-demo">
            <div class="row">
                <div class="col">
                    <h2>Available Batches</h2>
                </div>
            </div>

            {{--
            <div class="ui-group">
                <div class="filters button-group js-radio-button-group">
                    <ul class="row controls row-cols-lg-6 row-cols-md-3 row-cols-2 batch_btn g-1">
                    @foreach($unique_courses as $unique_course)
                        <li class="col"><button type="button" class="control btn-5 btn-{{$unique_course}}"
            data-filter=".residency">
            {{$unique_course}}</button>
            </li>
            @endforeach
            </ul>
        </div>
    </div>
    --}}

    <div class="row">
        <div class="col-12">
            <div class="">
                <div class=" py-3" style="width: 100%; overflow: auto;">
                    <table class="table w-100 d-none d-lg-table">
                        @if(!empty(json_decode($batches)))
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Details</th>
                                <th>Batch Name</th>
                                <th>Starting from</th>
                                <th>Days</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        @endif
                        <tbody>
                            @forelse($batches as $batch)
                            @if($loop->index == 10) @break @endif
                            <tr class="residency">
                                <td>{{$batch->course_name}}</td>
                                <td> <div class="col text-center"><a class="btn btn-success rounded-lg btn-batch" href="{{ url('batch-details/'.$batch->id)}}">Details</a></div></td>
                                <td>{{$batch->batch_name}}</td>
                                <td>{{ \Carbon\Carbon::parse($batch->start_date)->format('d/m/Y')}}</td>
                                <td>{{$batch->days}}</td>
                                <td>{{$batch->time}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center py-5 display-6 border-0">No Batch Available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                {{-- </div>
                <div class="py-3 w-100"> --}}
                    <table class="table w-100 d-lg-none d-table">
                        @if(!empty(json_decode($batches)))
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Batch Name</th>
                            </tr>
                        </thead>
                        @endif
                        <tbody>
                            @forelse($batches as $batch)
                            @if($loop->index == 10) @break @endif
                            <tr class="residency">
                                <td>
                                    {{$batch->course_name}}
                                </td>
                                <td>
                                    <p><a href="{{ url('batch-details/'.$batch->id)}}">{{$batch->batch_name}}</a></p>
                                    <p>{{ \Carbon\Carbon::parse($batch->start_date)->format('d/m/Y')}}</p>
                                    <p>{{$batch->days}}</p>
                                    <p>{{$batch->time}}</p>

                                    <p class="col text-center"><a class="btn btn-success rounded-lg btn-batch" href="{{ url('batch-details/'.$batch->id)}}">Details</a></p>

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-center py-5 display-6 border-0">No Batch Available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="row">
        <div class="col-12">
            <div class="batch_box">
                <div class=" py-3" style="width: 100%; overflow: auto;">
                    <div class="table w-100">
                        @if(!empty(json_decode($batches)))
                        <div class="d-flex justify-content-between">
                            <b class="border w-100 p-1">Course Name</b>
                            <b class="border w-100 p-1">Details</b>
                            <b class="border w-100 p-1">Batch Name</b>
                            <b class="border w-100 p-1">Starting from</b>
                            <b class="border w-100 p-1">Days</b>
                            <b class="border w-100 p-1">Time</b>
                        </div>
                        @endif
                        <div>
                            @forelse($batches as $batch)
                            <div class="d-flex w-100">
                                @if($loop->index == 10) @break @endif
                                <div class="d-flex flex-lg-row flex-column justify-content-between w-100">
                                    <span class="border w-100 p-1">{{$batch->course_name}}</span>
                                    <span class="border w-100 p-1"><a href="{{ url('batch-details/'.$batch->id)}}">details</a></span>
                                    <span class="border w-100 p-1">{{$batch->batch_name}}</span>
                                    <span class="border w-100 p-1">{{$batch->start_date}}</span>
                                    <span class="border w-100 p-1">{{$batch->days}}</span>
                                    <span class="border w-100 p-1">{{$batch->time}}</span>
                                </div>
                            </div>
                            @empty
                            <div>
                                <p class="text-center py-5 display-6 border-0">No Batch Available</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="row">
        <div class="col text-center py-4">
            <a href="{{url('batch')}}" class="btn btn-success rounded-lg">Read More Batch</a>
        </div>
    </div>
    </div>
    </div>
</section>


<!-- ================= Batch Part End ================= -->

<!-- ================= Testimonial Part Start ================= -->
@if(!empty(json_decode($doctors_reviews)))
<section id="testimonial_part">
    <div class="">
        <div class="row">
            <div class="col">
                <div class="header text-center">
                    <h2>What Our Doctors Say</h2>
                </div>
            </div>
        </div>

        <div class="row testimonial_slider text-left">
            @foreach($doctors_reviews as $doctors_review)
            <div class="item p-2">
                <div class="col-12 card">
                    <div class="card-body" style="height: 320px;">
                        <div class="img">
                            <img class="img-fluid w-100" src="<?php echo $doctors_review['image']; ?>"
                                alt="testimonial">
                        </div>
                        <p><span>"</span>
                            {{str_limit($doctors_review->comment, 300)}}
                            <span>"</span></p>
                        <h5>{{$doctors_review->name}}</h5>
                        <h6>{{$doctors_review->designation}}</h6>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ================= Testimonial Part End ================= -->


<!-- ================= FAQ Part Start ================= -->
<section id="batch_part">
    <div class="container px-sm-0">

        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="table__responsive col py-2" 
                        style="display: flex;
                        align-items: left;
                        text-align: left;
                        margin-left: 20%;
                        margin-right: 20%;font-size:20px">

                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="p-5 p-md-0">
            <div class="modal-content p-0">
                <div class="text-right">
                    <button type="button" class="btn-close px-2 border-0 shadow-none h2" data-dismiss="modal"
                        aria-label="Close">&times;</button>
                </div>
                <div class="modal-body p-0">
                    <a target="_blank" class="d-block" href="https://www.genesisbcscare.com">
                        <img src="{{ asset('images/bcs-website.png') }}" alt="" class="img-fluid w-100">
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= FAQ Part End ================= -->

<!-- ================= Facebook Part start ================= -->
@include('layouts.facebook')
<!-- ================= Facebook Part End ================= -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"></script>
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>  
@endsection
@section('js')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

<script type="text/javascript">

    $(document).ready(function() {
                
                $('.medical_college').select2({
                    minimumInputLength: 3,
                    placeholder: "Type your madical college name",
                    escapeMarkup: function (markup) { return markup; },
                    ajax: {
                        url: '/search-college',
                        dataType: 'json',
                        type: "GET",
                        quietMillis: 50,
                        data: function (term) {
                            return {
                                term: term
                            };
                        },

                        processResults: function (data) {
                            return {
                                results: $.map(data, function (item) {
                                    return { id:item.id, text: item.name };
                                })
                            };
                        }
                    }
                });
        });


     const segment = '{{request()->segment(1) }}';
    $(document).ready(function() {    
       
        
       
        if (segment=='register'){
            
            register();
        }
        $('.doctor_day_slider').slick({
            slidesToShow: 5,
            slidesToScroll: 1,
            autoplay: true,
            infinite: true,
            speed: 1000,
            autoplaySpeed: 4000,
            arrows: false,
            responsive: [{
                    breakpoint: 1121,
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                    }
                },
                {
                    breakpoint: 701,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    }
                }
            ]
        });
    });

    
</script>


<style>
    .profile-mk h3 {
        padding: 4px;
        margin: 10px;
        text-align: center;
        top: 0;
        right: 0px;
    }

    .active-on {
        padding: 10px;
        margin: 10px;
        border: 1px solid #44CA75;
        background: #44CA75;
        border-radius: 15px !important;

    }

    .active-on a {
        color: #fff;
        font-size: 20px;
        font-weight: 500;
        font-family: 'Poppins', sans-serif;
    }

    .active-on:hover {
        background: #0F77B7;
    }

    @media (max-width: 723px) {
        .active-on {
            display: block !important;

        }

    }
</style>
@endsection