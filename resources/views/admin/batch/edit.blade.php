@extends('admin.layouts.app')

@section('content')

    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i><a href="{{ url('/admin') }}">Home</a><i class="fa fa-angle-right"></i>
            </li>
            <li>{{ $title }}</li>
        </ul>
    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {!! Session::get('message') !!}</p>
        </div>
    @endif
    @if(Session::has('error'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-danger' }}" role="alert">
            <p> {!! Session::get('error') !!}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{ $title }}
                        @if($batch->payment_times > 1) 
                            <a href="{{ url('admin/batch/payment-option/'.$batch->id) }}" class="btn btn-xs btn-info">Payment Option</a>
                        @endif
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->

                    {!! Form::open(['action'=>['Admin\BatchController@update', $batch->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="text" name="name" value="{{ $batch->name }}" required value="{{ old('topic_name') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="text" name="year" value="{{ $batch->year ?? '' }}" list="year" class="form-control">
                                    <datalist id="year">
                                        <option value="{{ date('Y')+1 }}">
                                        <option value="{{ date('Y') }}">
                                        <option value="{{ date('Y')-1 }}">
                                    </datalist>
                                </div>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <label class="col-md-3 control-label">Batch Code (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input readonly type="number" name="batch_code" required value="{{ $batch->batch_code }}"  minlength="2" maxlength="2" class="form-control">
                                </div>
                            </div>
                        </div> --}}

                        <div class="form-group">
                            <label class="col-md-3 control-label">Seat Capacity(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="capacity" required value="{{ $batch->capacity }}" minlength="3" maxlength="3" class="form-control">
                                </div>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <label class="col-md-3 control-label">Start Range (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="start_index" required value="{{ $batch->start_index }}" minlength="3" maxlength="3" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">End Range (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="end_index" required value="{{ $batch->end_index }}" minlength="3" maxlength="3" class="form-control">
                                </div>
                            </div>
                        </div> --}}

                        <div class="form-group">
                            <label class="col-md-3 control-label">Branch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                @php  $branches->prepend('Select Branch', ''); @endphp
                                {!! Form::select('branch_id',$branches, $batch->branch_id,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">Branch Discount</label>
                            <div class="col-md-3" id="id_div_service_point_discount">
                                <label class="radio-inline">
                                    <input type="radio" name="service_point_discount" value="yes" {{  $batch->service_point_discount === "yes" ? "checked" : '' }} > Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="service_point_discount" value="no" {{  $batch->service_point_discount === "no" ? "checked" : '' }} > No
                                </label>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                @php $institute->prepend('Select Institute', ''); @endphp
                                {!! Form::select('institute_id', $institute, $batch->institute_id, ['class'=>'form-control','required'=>'required']) !!}<i></i>

                          </div>
                        </div>

                        <div class="course">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Course</label>
                                <div class="col-md-4">
                                    @php $course->prepend('Select Course', ''); @endphp
                                    {!! Form::select('course_id', $course, $batch->course_id, ['class'=>'form-control','required'=>'required']) !!}<i></i>
                                </div>
                            </div>
                        </div>

                        {{--<div class="form-group">
                            <div class="faculty">
                            <label class="col-md-3 control-label">Faculty</label>
                            <div class="col-md-4">
                                @php $faculty->prepend('Select Faculty', ''); @endphp
                                {!! Form::select('faculty_id', $faculty, $batch->faculty_id, ['class'=>'form-control']) !!}<i></i>
                            </div>
                            </div>
                        </div>--}}




                        <div class="session">
                            <div class="form-group">
                                <label class="col-md-3 control-label">Sessions (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                                <div class="col-md-4">
                                    @php  $sessions->prepend('Select Session', ''); @endphp
                                    {!! Form::select('session_id', $sessions, $batch->session_id ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
                                </div>
                            </div>
                        </div>



                        <div class="form-group faculties">
                            @if( $batch->institute_id != '4' )
                                    <label class="col-md-3 control-label">Faculty </label>
                                    <div class="col-md-4">
                                        @php  request()->get( 'prepend' ) != 'false' ?  $faculties->prepend( 'Select Faculty', '' ) : null; @endphp
                                        {!! Form::select( 'faculty_id[]',$faculties, old( 'faculty_id', $selected_faculties ),
                                        [   'class'=>'form-control  select2 ',
                                            'data-placeholder' => 'Select Faculty',
                                            'multiple' => 'multiple',
                                            'id'=>'faculty_id'] ) !!}<i></i>
                                    </div>
                                    <input type="checkbox" id="checkbox_faculty" > Select All
                            @endif
                        </div>


{{--                        <div class="faculties">--}}

{{--                        </div>--}}

                        <div class="form-group disciplines">
{{--                            @if($batch->institute_id == '4')--}}
                                    <label class="col-md-3 control-label">Discipline </label>
                                    <div class="col-md-4">
                                        @php  request()->get( 'prepend' ) != 'false' ?  $subjects->prepend('Select Discipline', '') : null; @endphp

                                        {!! Form::select('subject_id[]',$subjects,  old('subject_id', $selected_subjects ), ['class'=>'form-control  select2 ',
                                            'data-placeholder' => 'Select Discipline',
                                            'multiple' => 'multiple','id'=>'subject_id']) !!}
                                        <i></i>
                                    </div>

                                    <input type="checkbox" id="checkbox" > Select All
{{--                            @endif--}}
                        </div>


                        <div class="form-group">

                            <label class="col-md-3 control-label">Batch Type</label>
                            <div class="col-md-4" id="id_div_batch_type">
                                <label class="radio-inline">
                                    <input type="radio" name="batch_type" value="Regular" {{  $batch->batch_type === "Regular" ? "checked" : '' }} checked > Regular
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="batch_type" value="Exam" {{  $batch->batch_type === "Exam" ? "checked" : '' }}> Exam
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="batch_type" value="Low_Cost" {{  $batch->batch_type === "Low_Cost" ? "checked" : '' }}> Low Cost
                                </label>
                            </div>

                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">Admission Fee Type</label>
                            <div class="col-md-4" id="id_div_admission_fee_type">
                                <label class="radio-inline">
                                    <input type="radio" name="fee_type" value="Batch" {{  $batch->fee_type === "Batch" ? "checked" : '' }} checked > Batch
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="fee_type" value="Discipline_Or_Faculty" {{  $batch->fee_type === "Discipline_Or_Faculty" ? "checked" : '' }}> Discipline Or Faculty
                                </label>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Admission Fee (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="admission_fee" min="0" required value="{{ $batch->admission_fee }}" {{  $batch->fee_type === "Discipline_Or_Faculty" ? "ReadOnly" : '' }} class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Lecture Sheet Fee (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="lecture_sheet_fee" min="0" required value="{{ $batch->lecture_sheet_fee }}" {{  $batch->fee_type === "Discipline_Or_Faculty" ? "ReadOnly" : '' }} class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Discount From Regular (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="discount_from_regular" min="0" required value="{{ $batch->discount_from_regular }}" {{  $batch->fee_type === "Discipline_Or_Faculty" ? "ReadOnly" : '' }} class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Discount From Exam (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="discount_from_exam" min="0" required value="{{ $batch->discount_from_exam }}" {{  $batch->fee_type === "Discipline_Or_Faculty" ? "ReadOnly" : '' }} class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Minimum Payment (%) (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="minimum_payment" min="0" value="{{ $batch->minimum_payment }}" required class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Payment Times (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="payment_times" min="0" value="{{ $batch->payment_times }}" required class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">Full payment waiver (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="number" name="full_payment_waiver" value="{{ $batch->full_payment_waiver??'0' }}" min="0" required class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">Apply New Discount Rule</label>
                            <div class="col-md-3" id="id_div_apply_new_discount_rule">
                                <label class="radio-inline">
                                    <input type="radio" name="apply_new_discount_rule" value="yes" {{  $batch->apply_new_discount_rule === "yes" ? "checked" : '' }} > Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="apply_new_discount_rule" value="no" {{  $batch->apply_new_discount_rule === "no" ? "checked" : '' }} > No
                                </label>
                            </div>

                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-3 control-label">
                                <b>Addon Services</b>
                            </label>
                            <div class="col-md-6">
                                <div style="border: 1px dashed #ccc; border-radius: 8px; padding: 16px;">
                                    <div id="addon_slot_container" style="display: flex; flex-direction: column; gap: 8px;">
                                        @foreach ($batch->addon_services as $batch_addon_service)
                                        <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                                            <div style="flex-shrink: 1; flex-grow: 1;">
                                                <select type="text" name="addon_services[]"  class="form-control addon_services" required>
                                                    <option value="">-- Select Book Name --</option>
                                                    @foreach ($addon_services as $addon_service)
                                                        <option value="{{ $addon_service->id }}" {{ $batch_addon_service->id == $addon_service->id ? 'selected' : '' }}>
                                                            {{ $addon_service->sale_price }}TK / {{ $addon_service->regular_price }}TK | {{ $addon_service->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="button" onclick="removeAddonSlot(this.parentElement)" class="btn btn-danger btn-sm" value="X" style="flex-shrink: 0; flex-grow: 0; width: 40px;">
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="text-center">
                                        <button type="button" onclick="addNewAddonSlot()" class="btn btn-sm btn-success">
                                            + Add Slot
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch Details</label>
                            <div class="col-md-6">
                                <div class="input-icon right">
                                    <textarea id="details" name="details">{{ $batch->details ? $batch->details :'' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Is Show in Admission</label>
                            <div class="col-md-4" id="id_div_is_show_admission">
                                <label class="radio-inline">
                                    <input type="radio" name="is_show_admission" value="Yes" {{  $batch->is_show_admission === "Yes" ? "checked" : '' }} checked > Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_show_admission" value="No" {{  $batch->is_show_admission === "No" ? "checked" : '' }}> No
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Is Special</label>
                            <div class="col-md-4" id="id_div_is_show_admission">
                                <label class="radio-inline">
                                    <input type="radio" name="is_special" value="Yes" {{  old( 'is_special', $batch->is_special ) === "Yes" ? "checked" : '' }}> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_special" value="No" {{  old( 'is_special', $batch->is_special ) === "No" ? "checked" : '' }} > No
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Previous exam mendatory</label>
                            <div class="col-md-4">
                                <label class="radio-inline">
                                    <input type="radio" name="previous_exam_mendatory" value="1" {{  old( 'previous_exam_mendatory', $batch->previous_exam_mendatory ) ? "checked" : '' }}> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="previous_exam_mendatory" value="0" {{  old( 'previous_exam_mendatory', $batch->previous_exam_mendatory ) ? "" : 'checked' }} > No
                                </label>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <label class="col-md-3 control-label">Is EMI</label>
                            <div class="col-md-4" id="id_div_is_show_admission">
                                <label class="radio-inline">
                                    <input type="radio" id="is_emi_yes" name="is_emi" value="1" {{  old( 'is_emi', $batch->is_emi ) == 1 ? "checked" : '' }}> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="is_emi_no" name="is_emi" value="0" {{  old( 'is_emi', $batch->is_emi ) == 0 ? "checked" : '' }} > No
                                </label>
                            </div>
                        </div> --}}


                        {{-- <div class="form-group">
                            <label class="col-md-3 control-label">Is EMI</label>
                            <div class="col-md-4" id="id_div_is_show_admission">
                                <label class="radio-inline">
                                    <input type="radio" id="is_emi_yes" name="is_emi" value="Yes" {{  old( 'is_emi', ucfirst( request()->emi ) ) === "Yes" ? "checked" : '' }}> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="is_emi_no" name="is_emi" value="No" {{  old( 'is_emi' ) === "No" ? "checked" : (!request()->has( 'emi' ) ? 'checked':'') }} > No
                                </label>
                            </div>
                        </div> --}}

                        {{-- <div class="form-group" id="is-emi" style="display:none">
                            <label class="col-md-3 control-label">EMI TYPE(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <select name="emi_id" class="form-control" required>
                                        <option value="" selected>Select emi types</option>
                                        <option value="3 Months">3 Months</option>
                                        <option value="6 Months">6 Months</option>
                                    </select>
                                </div>
                            </div>
                        </div> --}}


                        <div class="form-group">
                            <label class="col-md-3 control-label">Is Show Lecture Sheet</label>
                            <div class="col-md-4" id="id_div_is_show_lecture_sheet_fee">
                                <label class="radio-inline">
                                    <input type="radio" id="is_show_lecture_sheet_fee_yes" name="is_show_lecture_sheet_fee" value="Yes" {{  $batch->is_show_lecture_sheet_fee === "Yes" ? "checked" : '' }} checked > Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="is_show_lecture_sheet_fee_no" name="is_show_lecture_sheet_fee" value="No" {{  $batch->is_show_lecture_sheet_fee === "No" ? "checked" : '' }}> No
                                </label>
                            </div>
                        </div>

                        @php
                        $class=($batch->is_show_lecture_sheet_fee === "No")?'package-none':'';
                        @endphp
                        <div class="form-group {{ $class }}" id="lecture-sheet" >
                            <label class="col-md-3 control-label">Select Lecture Sheet Package (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                @php $package_name->prepend('Select package', '') @endphp
                                {!! Form::select('package_id',$package_name, $batch->package_id,['class'=>'form-control']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Select shipment</label>
                            <div class="col-md-4" id="shipments">
                                <label class="radio-inline">
                                    <input type="radio" id="shipments" name="shipment" value="1" {{  old( 'shipment', $batch->shipment ) == 1 ? "checked" : '' }}> Single
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="shipments" name="shipment" value="2" {{  old( 'shipment', $batch->shipment ) == 2 ? "checked" : '' }} > Double
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Lecture Sheet Mobile No </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="text" name="lecture_sheet_mobile_no" min="11" max="11" value="{{ $batch->lecture_sheet_mobile_no }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">System Driven</label>
                            <div class="col-md-4" id="id_div_system_driven">
                                <label class="radio-inline">
                                    <input type="radio" name="system_driven" value="No" {{  $batch->system_driven === "No" ? "checked" : '' }}> No
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="system_driven" value="Optional" {{  $batch->system_driven === "Optional" ? "checked" : '' }}> Optional
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="system_driven" value="Mandatory" {{  $batch->system_driven === "Mandatory" ? "checked" : '' }}> Mandatory
                                </label>

                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $batch->status,['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch Expiration Date (<i
                                    class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">

                                <input type="date" name="expired_at" id="expired_at" autocomplete="off" class="form-control" value="{{ $batch->expired_at ?? '' }}">
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Expired Message Description (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-4">
                                <div class="input-icon right">
                                    <input type="text" name="expired_message"  value="{{ $batch->expired_message }}" class="form-control">
                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="form-actions">
                        <div class="row">
                            <div class="col-md-offset-3 col-md-9">
                                <button type="submit" class="btn btn-info">Submit</button>
                                <a href="{{ url('admin/batch') }}" class="btn btn-default">Cancel</a>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!-- END FORM-->

                </div>
            </div>

        </div>
    </div>



@endsection

@section('js')

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>

    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    <script src="{{asset('js/batch-discipline-faculty.js')}}" type="text/javascript"></script>



    <script type="text/javascript">
        function initSelect2(selector = ".select2") {
            $(selector).select2();
        }

        function addNewAddonSlot() {
            let divElement = document.createElement("div");
            divElement.setAttribute("style", "display: flex; gap: 8px; margin-bottom: 8px;")

            divElement.innerHTML = `
                <div style="flex-shrink: 1; flex-grow: 1;">
                    <select type="text" name="addon_services[]" class="form-control addon_services" required>
                        <option value="">-- Select Book Name --</option>
                        @foreach ($addon_services as $addon_service)
                            <option value="{{ $addon_service->id }}">
                                {{ $addon_service->sale_price }}TK / {{ $addon_service->regular_price }}TK | {{ $addon_service->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="button" onclick="removeAddonSlot(this.parentElement)" class="btn btn-danger btn-sm" value="X" style="flex-shrink: 0; flex-grow: 0; width: 40px;">
                `;

            document.getElementById('addon_slot_container').appendChild(divElement);
                
            return initSelect2(".addon_services");
        }

        function removeAddonSlot(slotDivElement) {
            return slotDivElement.parentElement.removeChild(slotDivElement);
        }

        // document.getElementById('is_show_lecture_sheet_fee_yes').onclick = () => {
        //     document.getElementById("lecture-sheet").style.display = "block";
        // }
        // document.getElementById('is_show_lecture_sheet_fee_no').onclick = () => {
        //     document.getElementById("lecture-sheet").style.display = "none";
        //     // document.getElementById("package_option").value = " ";
        // }

        //emi
        // document.getElementById('is_emi_yes').onclick = () => {
        // document.getElementById("is-emi").style.display = "block";
        // }
        // document.getElementById('is_emi_no').onclick = () => {
        // document.getElementById("is-emi").style.display = "none";
        // }
        //emi_end

        $(document).ready(function() {

            CKEDITOR.replace( 'details' );

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-course',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.course').html(data);
                        $('.faculty').html('');
                        $('.subject').html('');
                    }
                });
            })

            manage_disciplines_and_faculties( 'edit' );

            // $("body").on( "change", "[name='course_id']", function() {
            //     var institute_id = $("[name='institute_id']").val();
            //     var course_id = $(this).val();
            //     if(institute_id == '4'){
            //         $.ajax({
            //             headers: {
            //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //             },
            //             type: "POST",
            //             //url: '/admin/'+$("[name='url']").val(),
            //             url: '/admin/course-subjects',
            //             dataType: 'HTML',
            //             data: {course_id: course_id},
            //             success: function( data ) {
            //                 $('.subject').html('');
            //                 $('.subject').html(data);
            //             }
            //         });
            //     }
            // })

            $("body").on( "change", "[name='faculty_id']", function() {
                var faculty_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/faculty-subject',
                    dataType: 'HTML',
                    data: {faculty_id: faculty_id},
                    success: function( data ) {
                        $('.subject').html(data);
                    }
                });
            })

            $("body").on( "change", "[name='fee_type']", function() {
                var fee_type = $(this).val();
                if(fee_type == "Discipline_Or_Faculty")
                {
                    $("[name='admission_fee']").prop("disabled", true);
                    $("[name='lecture_sheet_fee']").prop("disabled", true);
                    $("[name='discount_from_regular']").prop("disabled", true);
                    $("[name='discount_from_exam']").prop("disabled", true);
                }
                if(fee_type == "Batch")
                {
                    $("[name='admission_fee']").prop("disabled", false);
                    $("[name='lecture_sheet_fee']").prop("disabled", false);
                    $("[name='discount_from_regular']").prop("disabled", false);
                    $("[name='discount_from_exam']").prop("disabled", false);
                }
            });
            $('.package-none').css("display", "none");
            $("#is_show_lecture_sheet_fee_no").click(function(){
                // alert(10)
                $('.package-none').css("display", "none");
            });

            $("#is_show_lecture_sheet_fee_yes").click(function(){
                // alert(10)
                $('.package-none').css("display", "block");
            });

        })
    </script>




@endsection
