@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row">

        @include('side_bar')

        <div class="col-md-9 col-md-offset-0">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">
                        <h2 class="h2 brand_color">{{ 'My Profile' }}</h2>
                    </div>
                </div>

                {{-- <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Home</button>
                        <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Profile</button>
                        <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Contact</button>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">HHH</div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">III</div>
                    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">JJJ</div>
                </div> --}}





                <div class="panel-body p-4">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="col-md-2 col-md-offset-0">
                            @php if ($doc_info->photo) {$photo = $doc_info->photo;} else {$photo="logo.png";} @endphp
                            <img src="{{url($photo)}}" width="100" height="100" >
                        </div>

                        <div class="col-md-5 col-md-offset-0 mt-3">
                            <h4><b>{{ $doc_info->name }}</b></h4>
                        </div>

                        <hr>

                        <div class="col-md-5 col-md-offset-0">
                            <h5> Father : {{ $doc_info->father_name }}</h5>
                            <h5> Mother : {{ $doc_info->mother_name }}</h5>
                        </div>

                        <div class="col-md-5 col-md-offset-0" style="">
                            <h5> BMDC No. : {{ $doc_info->bmdc_no }}</h5>
                            <h5> Mobile : {{ $doc_info->mobile_number }}</h5>
                            <h5> Email : {{ $doc_info->email }}</h5>
                        </div>

                        <div class="col-md-12 col-md-offset-0" style="">
                            <hr>
                        </div>

                        <div class="col-md-6 col-md-offset-0" style="">
                            <h5> Date of Birth : {{ $doc_info->date_of_birth }}</h5>
                            <h5> Gender : {{ $doc_info->gender }}</h5>
                            <h5> Blood Group : {{ $doc_info->blood_group }}</h5>
                        </div>

                        <div class="col-md-6 col-md-offset-0" style="">
                            <h5> Medical College : {{ (isset($doc_info->medicalcolleges->name))?$doc_info->medicalcolleges->name:'' }}</h5>
                            <h5> Facebook ID : {{ $doc_info->facebook_id }}</h5>
                        </div>

                        <div class="col-md-12 col-md-offset-0" style="">
                            <hr>
                        </div>

                        <div class="col-md-6 col-md-offset-0" style="">
                            <h5> NID : {{ $doc_info->nid }}</h5>
                            <h5> Passport : {{ $doc_info->passport }}</h5>
                            <h5> Job Description : {{ $doc_info->job_description }}</h5>
                        </div>

                        <div class="col-md-6 col-md-offset-0" style="">


                        </div>

                        <div class="col-md-12 col-md-offset-0" style="">
                            <hr>
                        </div>

                        <div class="col-md-6 col-md-offset-0" style="">
                            <h5> Present Address : {{ $doc_info->present_address }}</h5>
                            <h5> Upazila : {{ (isset($doc_info->present_upazila->name))? $doc_info->present_upazila->name : '' }}</h5>
                            <h5> District : {{ (isset($doc_info->present_district->name))? $doc_info->present_district->name : '' }}</h5>
                            <h5> Division : {{ (isset($doc_info->present_division->name))? $doc_info->present_division->name : '' }}</h5>
                        </div>

                        <div class="col-md-6 col-md-offset-0" style="">
                            <!-- <h5> Permanent Address : {{ $doc_info->permanent_address }}</h5>
                            <h5> Upazila : {{ (isset($doc_info->permanent_thana->name))? $doc_info->permanent_thana->name : '' }}</h5>
                            <h5> District : {{ (isset($doc_info->permanent_district->name))? $doc_info->permanent_district->name : '' }}</h5>
                            <h5> Division : {{ (isset($doc_info->permanent_division->name))? $doc_info->permanent_division->name : '' }}</h5> -->
                        
                        </div>

                        <div class="col-md-12 col-md-offset-0" style="">
                            <hr>
                        </div>

                        <div class="col-md-12 col-md-offset-0" style="">
                            <a href="{{ url('my-profile/edit/'.$doc_info->id) }}" class="btn btn-xs btn-primary">Edit Profile</a>


{{--                            <a href="#" class="btn btn-xs btn-primary">Enter Institute Roll</a>--}}

                        </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@section('js')
    <script>

        var triggerTabList = [].slice.call( document.querySelectorAll( '#nav-tab .nav-link' ) );

        triggerTabList.forEach( function ( triggerEl ) {


            triggerEl.addEventListener('click', function ( event ) {
                event.preventDefault();
                var tabTrigger = new bootstrap.Tab( this );
                console.log( tabTrigger );
                tabTrigger.show();
            });
        });

    </script>
@endsection
