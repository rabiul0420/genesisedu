@extends('layouts.app')

@section('content')
<style>

.page-breadcrumb {
  display: inline-block;
  float: left;
  padding: 8px;
  margin: 0;
  list-style: none;
}
.page-breadcrumb > li {
  display: inline-block;
}
.page-breadcrumb > li > a,
.page-breadcrumb > li > span {
  color: #666;
  font-size: 14px;
  text-shadow: none;
}
.page-breadcrumb > li > i {
  color: #999;
  font-size: 14px;
  text-shadow: none;
}
.page-breadcrumb > li > i[class^="icon-"],
.page-breadcrumb > li > i[class*="icon-"] {
  color: gray;
}
.bg {
    background: #a6ecc5;
    color: #0f77b7;
}

</style>

<div class="container">


    <div class="row">

        @include('side_bar')

        <div class="col-md-9">
            <div class="panel panel-default pt-2">
                <div class="panel_box w-100 bg-white rounded shadow-sm">
                    <div class="header text-center py-3">

                        <div class="row" style="margin-left: 50px;">
                            <div class="col col-lg-10">
                                <h2 class="h2 brand_color">{{ 'Schedule' }}</h2>
                            </div>
                            
                        </div>
                    </div>
                </div>
                
                <div class="my-3">
                </div>

                <div class="panel-body">
                    @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                    @endif


                    <div class="col-md-12 p-0">
                        <div class="portlet">
                            <div class="portlet-body">

                                <div class="row mx-0">
                                    @foreach( $doctor_courses as $doctor_course )

                                        <div class="col-md-6">
                                            @php $batchInactive = ( $doctor_course->batch->status ?? '' ) == 0; @endphp
                                            <a title="{{ ($doctor_course->course->name ?? '').' : '. ( $doctor_course->batch->name ?? '' ) }}"
                                                class="w-100 col-md-12 px-3 py-2 my-1 border bg rounded-lg shadow-sm {{ $batchInactive == 1 ? '' : 'batch-schedule-id' }}" data-id="{{ $doctor_course->id }}"
                                                href="{{ $batchInactive == 1 ? 'javascript:void(0)' : url('doctor-course-schedule/'.$doctor_course->id ) }}" data-batch_id="{{ $doctor_course->batch_id }}"
                                                data-doctor_id="{{ Auth::guard('doctor')->id() }}" data-status="{{ $doctor_course->batch->status }}" data-batch-id="{{ $doctor_course->batch_id }}" data-doctor-course-id="{{ $doctor_course->doctor_course_id }}" data-system-driven="{{ $doctor_course->batch->system_driven ?? '' }}" data-doctor-course-system-driven-changed="" data-doctor-course-system-driven="{{ $doctor_course->doctor_course_system_driven ?? '' }}"
                                            >
                                                <span class=" btn-info mb-2 btn btn-secondary"> Go To Schedule </span>
                                                <h6 class="bg" >{{ ( $doctor_course->course->name ?? '').' : '.($doctor_course->batch->name ?? '') }}</h6>

                                                @if(  $doctor_course->batch->expired_at  )
                                                <br>
                                                {{-- <span class="badge bg-info">Batch will be expired after, {{ $doctor_course->batch->expired_at  }} </span> --}}
                                                <span class="badge bg-info">Batch will be inactivated after: {{ \Carbon\Carbon::parse($doctor_course->batch->expired_at)->format('d M, Y') }} </span> . <br>
                                                <span class="badge bg-info">{{ $doctor_course->batch->expired_message }} </span>
                                                @endif
                                                {{-- @if( ( $doctor_course->batch->status ?? '' ) == 0 )
                                                    <span class="badge bg-danger">Batch Inactive</span>
                                                @endif --}}
                                            </a>
                                        </div>


                                        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">{{ ($doctor_course->course->name ?? '') . ' : ' . ($doctor_course->batch->name ?? '') }}</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                                </div>
                                                <div class="modal_body" style="padding: 20px;">
                                                    {!! $doctor_course->terms_and_condition !!}
                                                </div>
                                                <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <a type="button" class="btn btn-primary" href="{{ ($doctor_course->batch->status ?? '') === 0 ? 'javascript:void(0)' : url('new-schedule/' . ($doctor_course->id ?? '').'/'.$doctor_course->doctor_course_id) }}">Open</a>
                                                </div>
                                            </div>
                                            </div>
                                        </div>



                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modal-id"
        aria-hidden="true">

            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modal-id">Please update your batch informations</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close">&times;</button>
                    </div>

                    <form id="modal-form" action="" method="POST">

                    </form>

                </div>
            </div>

        </div>

        <!-- Modal -->
        <div class="modal fade" id="system_driven" tabindex="-1" role="dialog" aria-labelledby="system_driven_header" aria-hidden="false">
            <div class="modal-dialog system_driven_dialog">
            <div class="modal-content">
                <!-- <div class="modal-header">
                <h5 class="modal-title" id="system_driven_header">Syestem Driven</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="false"></span>
                </button>
                </div>
                <div class="modal-body system_driven_body">
                ...
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div> -->
            </div>
            </div>
        </div>

    </div>


</div>
@endsection

@section('js')
    <script type="text/javascript">

        

        $(document).ready(function() {


           




        })
    </script>
@endsection
