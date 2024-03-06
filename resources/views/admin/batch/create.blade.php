@extends('admin.layouts.app')

@section('content')


    <div class="page-bar">
        <ul class="page-breadcrumb">
            <li>
                <i class="fa fa-home"></i>
                <a href="{{ url('/') }}">Home</a>
                <i class="fa fa-angle-right"></i>
            </li>
            <li>{{$title}}</li>
        </ul>

    </div>

    @if(Session::has('message'))
        <div class="alert {{ (Session::get('class'))?Session::get('class'):'alert-success' }}" role="alert">
            <p> {{ Session::get('message') }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-reorder"></i>{{$title}}
                    </div>
                </div>
                <div class="portlet-body form">
                    <!-- BEGIN FORM-->
                    {!! Form::open(['action'=>['Admin\BatchController@store'],'files'=>true,'class'=>'form-horizontal']) !!}
                    <div class="form-body">

                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch Name (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="name" required value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Year (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <select name="year" class="form-control">
                                        <option value="">Select year</option>
                                        <option {{ old('year')==(date('Y')+1) ? 'selected' : '' }} value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>
                                        <option {{ old('year')==(date('Y')) ? 'selected' : '' }} value="{{ date('Y') }}">{{ date('Y') }}</option>
                                        <option {{ old('year')==(date('Y')-1) ? 'selected' : '' }} value="{{ date('Y')-1 }}">{{ date('Y')-1 }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Seat Capacity(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="capacity" required value="{{ old('capacity') }}" minlength="3" maxlength="3" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Branch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $branches->prepend('Select Branch', ''); @endphp
                                {!! Form::select('branch_id',$branches, old('branch_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">Branch Discount</label>
                            <div class="col-md-3" id="id_div_service_point_discount">
                                <label class="radio-inline">
                                    <input type="radio" name="service_point_discount" value="yes" {{  old('service_point_discount') === "yes" ? "checked" : '' }} > Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="service_point_discount" value="no" {{  old('service_point_discount') === "no" ? "checked" : '' }} checked > No
                                </label>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Institute (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                @php  $institute->prepend('Select Institute', ''); @endphp
                                {!! Form::select('institute_id',$institute, old('institute_id'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="course">

                        </div>

                        <div class="session">

                        </div>

                        <div class="faculties">

                        </div>

                        <div class="disciplines">

                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">Batch Type</label>
                            <div class="col-md-3" id="id_div_batch_type">
                                <label class="radio-inline">
                                    <input type="radio" name="batch_type" value="Regular" {{  old('batch_type') === "Regular" ? "checked" : '' }} checked > Regular
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="batch_type" value="Exam" {{  old('batch_type') === "Exam" ? "checked" : '' }}> Exam
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="batch_type" value="Low_Cost" {{  old('batch_type') === "Low_Cost" ? "checked" : '' }}> Low Cost
                                </label>
                            </div>

                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">Admission Fee Type</label>
                            <div class="col-md-3" id="id_div_admission_fee_type">
                                <label class="radio-inline">
                                    <input type="radio" name="fee_type" value="Batch" {{  old('fee_type') === "Batch" ? "checked" : '' }} checked > Batch
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="fee_type" value="Discipline_Or_Faculty" {{  old('fee_type') === "Discipline_Or_Faculty" ? "checked" : '' }}> Discipline Or Faculty
                                </label>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Admission Fee (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="admission_fee" min="0" required value="0" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Lecture Sheet Fee (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="lecture_sheet_fee" min="0" required value="0" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Discount From Regular (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="discount_from_regular" min="0" required value="0" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Discount From Exam (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="discount_from_exam" min="0" required value="0" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Minimum Payment (%) (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="minimum_payment" min="0" value="100" required class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Payment Times (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="payment_times" value="0" min="0" required class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Full payment waiver (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="number" name="full_payment_waiver" value="0" min="0" required class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">Apply New Discount Rule</label>
                            <div class="col-md-3" id="id_div_apply_new_discount_rule">
                                <label class="radio-inline">
                                    <input type="radio" name="apply_new_discount_rule" value="yes" {{  old('apply_new_discount_rule') === "yes" ? "checked" : '' }} > Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="apply_new_discount_rule" value="no" {{  old('apply_new_discount_rule') === "no" ? "checked" : '' }} > No
                                </label>
                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch Details</label>
                            <div class="col-md-9">
                                <div class="input-icon right">
                                    <textarea id="details" name="details">{{ old('details')?old('details'):'' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Is Show in Admission</label>
                            <div class="col-md-3" id="id_div_is_show_admission">
                                <label class="radio-inline">
                                    <input type="radio" name="is_show_admission" value="Yes" {{  old('is_show_admission') === "Yes" ? "checked" : '' }} checked > Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_show_admission" value="No" {{  old('is_show_admission') === "No" ? "checked" : '' }}> No
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Is Special</label>
                            <div class="col-md-3" id="id_div_is_show_admission">
                                <label class="radio-inline">
                                    <input type="radio" name="is_special" value="Yes" {{  old( 'is_special', ucfirst( request()->special ) ) === "Yes" ? "checked" : '' }}> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="is_special" value="No" {{  old( 'is_special' ) === "No" ? "checked" : (!request()->has( 'special' ) ? 'checked':'') }} > No
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Previous exam mendatory</label>
                            <div class="col-md-3">
                                <label class="radio-inline">
                                    <input type="radio" name="previous_exam_mendatory" value="1" {{  old( 'previous_exam_mendatory', request()->previous_exam_mendatory ) ? "checked" : '' }}> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="previous_exam_mendatory" value="0" {{  old( 'previous_exam_mendatory', request()->previous_exam_mendatory ) ? "" : 'checked' }} > No
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Is Show Discount</label>
                            <div class="col-md-3" id="discount_fee">
                                <label class="radio-inline">
                                    <input type="radio" name="discount_fee" value="Yes" {{  old('discount_fee') === "Yes" ? "checked" : '' }} checked > Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="discount_fee" value="No" {{  old('discount_fee') === "No" ? "checked" : '' }}> No
                                </label>
                            </div>
                        </div>

                        {{-- <div class="form-group">
                            <label class="col-md-3 control-label">Is EMI</label>
                            <div class="col-md-3" id="id_div_is_show_admission">
                                <label class="radio-inline">
                                    <input type="radio" id="is_emi_yes" name="is_emi" value="1" {{  old( 'is_emi', ucfirst( request()->emi ) ) === "Yes" ? "checked" : '' }}> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="is_emi_no" name="is_emi" value="0" {{  old( 'is_emi' ) === "No" ? "checked" : (!request()->has( 'emi' ) ? 'checked':'') }} > No
                                </label>
                            </div>
                        </div> --}}

                        {{-- <div class="form-group" id="is-emi" style="display:none">
                            <label class="col-md-3 control-label">EMI TYPE(<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <select name="emi_id" class="form-control">
                                        <option value="" selected>Select emi types</option>
                                        <option value="3 Months">3 Months</option>
                                        <option value="6 Months">6 Months</option>
                                    </select>
                                </div>
                            </div>
                        </div> --}}

                        <div class="form-group">
                            <label class="col-md-3 control-label">Is Show Lecture Sheet</label>
                            <div class="col-md-3">
                                <label class="radio-inline">
                                    <input type="radio" id="is_show_lecture_sheet_fee_yes" name="is_show_lecture_sheet_fee"  value="Yes"> Yes
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="is_show_lecture_sheet_fee_no" name="is_show_lecture_sheet_fee" value="No" > No
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="lecture-sheet" style="display:none">
                            <label class="col-md-3 control-label">Select Lecture Sheet Package (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <select name="package_id" class="form-control" required>
                                        <option value=" " selected>Select Lecture Sheet Size</option>
                                        @foreach ($package_name as $key=>$name)
                                            <option value="{{$key}}">{{$name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="shipments">

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Lecture Sheet Mobile No </label>
                            <div class="col-md-3">
                                <div class="input-icon right">
                                    <input type="text" name="lecture_sheet_mobile_no" min="11" max="11" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">

                            <label class="col-md-3 control-label">System Driven</label>
                            <div class="col-md-3" id="id_div_system_driven">
                                <label class="radio-inline">
                                    <input type="radio" name="system_driven" value="No" {{  old('system_driven') === "No" ? "checked" : '' }} checked> No
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="system_driven" value="Optional" {{  old('system_driven') === "Optional" ? "checked" : '' }}> Optional
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="system_driven" value="Mandatory" {{  old('system_driven') === "Mandatory" ? "checked" : '' }} > Mandatory
                                </label>

                            </div>

                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Select Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], old('status'),['class'=>'form-control','required'=>'required']) !!}<i></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch Expiration Date (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                            <div class="col-md-3">
                                <input type="date" name="expired_at" id="expired_at" autocomplete="off" class="form-control" value="{{ $batch->expired_at ?? '' }}">
                            </div>
                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Expired Message (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                        <div class="col-md-3">
                            <div class="input-icon right">
                                <input type="text" name="expired_message"  value="{{ old('expired_message') }}" class="form-control">
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

                </div>
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->

@endsection

@section('js')
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.js" type="text/javascript"></script>
    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

    <script src="{{asset('js/batch-discipline-faculty.js')}}" type="text/javascript"></script>

    <script type="text/javascript">
    document.getElementById('is_show_lecture_sheet_fee_yes').onclick = () => {
        document.getElementById("lecture-sheet").style.display = "block";
    }
    document.getElementById('is_show_lecture_sheet_fee_no').onclick = () => {
        document.getElementById("lecture-sheet").style.display = "none";
    }

    // document.getElementById('is_emi_yes').onclick = () => {
    //     document.getElementById("is-emi").style.display = "block";
    // }
    // document.getElementById('is_emi_no').onclick = () => {
    //     document.getElementById("is-emi").style.display = "none";
    // }

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



            manage_disciplines_and_faculties( 'create' );


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
            });

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

            $("body").on("change", "[name='package_id']", function(){
                var package_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/shipment',
                    dataType: 'HTML',
                    data: {package_id: package_id},
                    success: function( data ) {
                        $('.shipments').html(data);
                    }
                });
            });
        })
    </script>

@endsection
