@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>
                Batches Schedules {{ $action  }}
            </li>
        </ul>
    </div>



    @if( Session::has( 'message' ) )
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {!! Session::get('message') !!}</p>
        </div>

    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12" id="application">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{ ucfirst( $action ) }} Batch Schedule
                    </div>
                </div>


                <div style="display: none">
                    <div id="empty-schedule-content">
                        @include('admin.batch_schedules.schedule_slot.content', ['isNewContent' => true ])
                    </div>
                    <div id="empty-schedule-child-content">
                        @include('admin.batch_schedules.schedule_slot.content', ['isChild' => true, 'isNewContent' => true ])
                    </div>
                    <div id="empty-schedule-slot">
                        @include('admin.batch_schedules.schedule_slot.slot')
                    </div>
                    <div id="empty-fb-link">
                        @include('admin.batch_schedules.fb-link.fb-link-item', [ 'item' => [], 'index' => '{index}' ] )
                    </div>
                </div>

                <div class="portlet-body form">

                    <form class="form-horizontal"
                          method="post"
                          encType="multipart/form-data"
                          action="{{ route( $action == 'edit'? 'batch-schedule-edit-v2' : 'batch-schedule-store-v2', $schedule->id ?? null ) }}"
                    >

                        {{ csrf_field() }}

                        {{ method_field( $action == 'edit' ? 'PUT':'POST' ) }}

                        <div class="form-body">

                            {{--Basic--}}
                            <div class="panel panel-primary">
                                <div class="panel-heading">Select Batches Schedules Information</div>
                                <div class="panel-body">

                                    <div>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Schedule Name (<span class="fa fa-asterisk ipd-star" style="font-size:9px"></span>) </span> </label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    <input  required type="text" name="name"
                                                            value="{{ old('name', $schedule->name ?? '' )  }}"
                                                            placeholder="e.g: Specila Schedule - March'20"
                                                            class="form-control"/>
                                                </div>
                                            </div>

                                            <label class="col-md-2 control-label">Schedule Sub Line </label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    <input  type="text" name="tag_line"
                                                            value="{{ old('tag_line', $schedule->tag_line ?? '' ) }}"
                                                            placeholder="e.g: Every Monday & Thursday"
                                                            class="form-control"/>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Schedule Contact Details (<span class="fa fa-asterisk ipd-star" style="font-size:9px"></span>) </label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    <input required type="text" name="contact_details"
                                                           value="{{old('contact_details', $schedule->contact_details ?? '' )}}"
                                                           placeholder="Schedule Contact Person and Mobile No"
                                                           class="form-control"/>
                                                </div>
                                            </div>
                                            <label class="col-md-2 control-label">Executive (<span class="fa fa-asterisk ipd-star" style="font-size:9px"></span>) </label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    {{
                                                        Form::select('executive_id', $executive_list, old('executive_id', $schedule->executive_id ?? '' ),
                                                            ['class' => 'form-control', 'required' => 'required']
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Room Number (<span class="fa fa-asterisk ipd-star" style="font-size:9px"></span>) </label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    {{
                                                        Form::select( 'room_id', $rooms_types, old('room_id', $schedule->room_id ?? '' ),
                                                            ['class' => 'form-control', 'required' => 'required']
                                                        )
                                                    }}
                                                </div>
                                            </div>

                                            <label class="col-md-2 control-label">Address (<span class="fa fa-asterisk ipd-star" style="font-size:9px"></span>) </label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    <textarea required
                                                              name="address"
                                                              class="form-control"
                                                              placeholder="Type address here">{{old('address', $schedule->address ?? '' )}}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-2 control-label">Terms & Condition (<span class="fa fa-asterisk ipd-star" style="font-size:9px"></span>) </label>
                                            <div class="col-md-6">
                                                <div class="input-icon right">
                                                    <textarea id="terms-and-condition"  required type="text"
                                                              name="terms_and_condition" rows="5"
                                                               placeholder="e.g: Specila Schedule - March'20"
                                                              class="form-control">{{old('terms_and_condition', $schedule->terms_and_condition ?? '' )}}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                    </div>



                                {{--End of Basic--}}
                                </div>
                            </div>
                            {{--End of Basic--}}

                            {{-- Select Batches Schedules Course Information --}}

                            <div class="panel panel-primary">
                                <div class="panel-heading">Select Batches Schedules Course Information</div>
                                <div class="panel-body">
                                    <div>
                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Class Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    {!!
                                                        Form::select( 'year', $years, old( 'year', $schedule->year ?? '' ),
                                                        [ 'class' => 'form-control year', 'id' => 'year', 'required' => 'required' ] )
                                                    !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div id="institutes">
                                            {!! $institutes_view ?? '' !!}
                                        </div>

                                        <div id="courses">
                                            {!! $courses_view ?? '' !!}
                                        </div>

                                        <div id="sessions">
                                            {!! $sessions_view ?? '' !!}
                                        </div>
                                        <div id="batches">
                                            {!! $batches_view ?? '' !!}
                                        </div>

                                        <div id="faculties">
                                            {!! $faculties_view ?? '' !!}
                                        </div>

                                        <div id="disciplines">
                                            {!! $bcps_discipline_view ?? '' !!}
                                        </div>


                                        <div class="form-group">
                                            <label class="col-md-3 control-label">Status (<span class="fa fa-asterisk ipd-star" style="font-size:9px"></span>) </label>
                                            <div class="col-md-3">
                                                <div class="input-icon right">
                                                    {{
                                                        Form::select( 'status', [ 1 => 'Active',  0 => 'InActive' ], old('status', $schedule->statis ?? '' ),
                                                            ['class' => 'form-control', 'required' => 'required']
                                                        )
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Endof Select Batches Schedules Course Information --}}

                            <div class="panel panel-primary" >
                                <div class="panel-heading">Social Media Link</div>
                                <div class="panel-body" id="fb-links">

                                    <div class="fb-link-items">
                                        @php $ind = 0; @endphp
                                        @foreach( ($fb_links ?? []) as $fb_link )
                                            @include( 'admin.batch_schedules.fb-link.fb-link-item', [ 'item' => $fb_link, 'index' => $ind ] )
                                            @php $ind++; @endphp
                                        @endforeach
                                    </div>

                                    <div class="fb-link-actions">
                                        <div class="col-md-12 text-center">
                                            <button class="btn btn-info add-fb-link-btn">+ Add</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="panel panel-primary" id="exam-class">
                                <div class="panel-heading">Add/Remove class or exam</div>
                                <div class="panel-body">

                                    <div class="container-fluid">
                                        <div class="row" id="schedule-slots">
                                            @if( ($schedule->time_slots ?? []) instanceof \Illuminate\Support\Collection )

                                                @foreach( ($schedule->time_slots ?? []) as $slot )
                                                    @include( 'admin.batch_schedules.schedule_slot.slot', ['slot' => $slot ] )
                                                @endforeach

                                            @endif
                                        </div>
                                    </div>

                                    <div class="container-fluid">
                                        <div class="row">
                                            <button class="btn btn-success" id="slot-add-btn">+Add Slot</button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-lg btn-success">Submit</button>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
            </div>

        </div>
    </div>

{{--    <pre>--}}
{{--        {!! print_r( $schedule )  !!}--}}
{{--    </pre>--}}
@endsection

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/datepicker.css') }}" />
    <link ref="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.css') }}" rel="stylesheet">
@endsection

@section('js')
    @include('admin.batch_schedules.script')
@endsection