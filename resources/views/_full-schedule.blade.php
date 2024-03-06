@extends('layouts.app')
@section('content')
    <div class="container card-top">
        <div class="card mt-5 mb-5">

            <div class="navbar">

                <div class="container-fluid">
                    <div>
                        <p class="navbar-brand pt-4">Full Schedule</p>
                        <a href="{{ route('full-schedule', [$schedule_id]) }}" class="btn btn-outline-info">Full Schedule at a glance</a>
                    </div>
                </div>

            </div>

            <div class="container-fluid class-or-exam-contents" style="margin-bottom: 25px; ">
                <div class="row" style="padding: 10px; border-bottom: 1px solid #ddd">
                    <div class="col-12 col-md-3 col-lg-2 text-center">Date</div>
                    <div class="col-12 col-md-6 col-lg-10">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-5  text-center">Class</div>
                            <div class="col-12 col-md-5 col-lg-5  text-center">Exam</div>
                        </div>
                    </div>
                </div>

                <div class="row" style="padding: 10px; border-bottom: 1px solid #ddd">
                    <div class="col-12 col-md-3 col-lg-2 text-center">8/8/21</div>
                    <div class="col-12 col-md-6 col-lg-10">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-5  text-center">Inflamation 1</div>
                            <div class="col-12 col-md-5 col-lg-5  text-center">Neoplasm-1</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

@endsection

