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
            doctor course edit
        </li>
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
      <i class="fa fa-reorder"></i>Doctor Course Edit 
      @if($doctor_course->batch->system_driven == "Optional") 
        <a href="{{ url('/admin/doctor-course-system-driven/'.$doctor_course->id) }}" class="btn btn-xs btn-primary">System Driven</a>
      @endif
      @if(isset($doctor_course->doctor_exams) && count($doctor_course->doctor_exams)) 
        <a href="{{ url('/admin/doctor-exams/'.$doctor_course->id) }}" class="btn btn-xs btn-primary">Doctor Exams</a>
      @endif
      @if($doctor_course->batch->payment_times > 1 && $doctor_course->payment_status != "Completed") 
        <a href="{{ url('admin/doctor-course/payment-option/'.$doctor_course->id) }}" class="btn btn-xs btn-info">Payment Option</a>
      @endif
      @if($doctor_course->payment_status != "Completed" ||  ($doctor_course->paid_amount() < $doctor_course->course_price))
        <a href="{{ url('admin/doctor-course/payments/'.$doctor_course->id) }}" class="btn btn-xs btn-success">Pay Now</a>
      @endif
      @if($doctor_course->payment_status == "Completed")
        <a href="{{ url('admin/doctor-course/payment-history/'.$doctor_course->id) }}" class="btn btn-xs btn-success">Payment History</a>
      @endif     
     </div>
     
     
    </div>
    <div class="portlet-body form">
     <div>
        
     </div>
     <!-- BEGIN FORM-->
     {!! Form::open(['action'=>['Admin\DoctorsCoursesController@update',$doctor_course->id],'method'=>'PUT','files'=>true,'class'=>'form-horizontal']) !!}
        <div class="form-body">
            <div class="panel panel-primary" style="border-color: #eee; ">
                <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee;">
                    Doctor Information
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Doctor Name</label>
                        <div class="col-md-3">
                            <div class="form-control">{{ $doctor_course->doctor->name ?? '' }}</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Refer By</label>
                        <div class="col-md-3">
                            <input type="text" name="refer_by" value="{{ $doctor_course->refer_by }}" class="form-control">
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-primary" style="border-color: #eee; ">
                <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee;">
                    Batch Sifted Information
                </div>
                <div class="panel-body">
                    @if($doctor_course->batch_shifted == 1 )
                    @if(false)
                    <div class="form-group">
                        <label class="col-md-3 control-label">Batch Shifted (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                        <div class="col-md-3">
                            <label class="radio-inline">
                                <input type="radio" name="batch_shifted" id="batch_shifted_yes" required value="1" {{  $doctor_course->batch_shifted == "1" ? "checked" : '' }}  /> Yes
                            </label>
                            {{-- <label class="radio-inline">
                                <input type="radio" name="batch_shifted" id="batch_shifted_no" required  value="0" {{  $doctor_course->batch_shifted == "0" ? "checked" : '' }} /> No
                            </label> --}}
                        </div>
                    </div>
                    @endif
                    @else
                    <div class="form-group">
                        <label class="col-md-3 control-label">Batch Shifted (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                        <div class="col-md-3">
                            @if($doctor_course->is_trash || $doctor_course->payment_status === 'No Payment')
                            <div class="form-control text-danger">
                                Shifting not applicable
                            </div>
                            <input type="hidden" name="batch_shifted" value="0" />
                            @else
                            <label class="radio-inline">
                                <input type="radio" name="batch_shifted" id="batch_shifted_yes" required value="1" {{  $doctor_course->batch_shifted == "1" ? "checked" : '' }}  /> Yes
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="batch_shifted" id="batch_shifted_no" required  value="0" {{  $doctor_course->batch_shifted == "0" ? "checked" : '' }} /> No
                            </label>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($doctor_course->batch_shifted == 1 )
                    <div id="batch_shifted_info_div" style="display:block;">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch Shifted To </label>
                            <div class="col-md-6">
                                <div class="form-control">
                                    <a target="_blank" href="{{ route('doctors-courses.edit', $doctor_course->batch_shift_history->to_doctor_course_id) }}">
                                        <b>({{ $doctor_course->batch_shift_history->to_doctor_course->reg_no ?? '' }})</b>
                                    </a>
                                    {{ $doctor_course->batch_shift_history->to_doctor_course->batch->name ?? '' }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch Shift Fee</label>
                            <div class="col-md-6">
                                <div class="form-control">
                                    {{ $doctor_course->batch_shift_history->shift_fee ?? 0 }} TK
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Maintenance Charge</label>
                            <div class="col-md-6">
                                <div class="form-control">
                                    {{ $doctor_course->batch_shift_history->service_charge ?? 0 }} TK
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Note</label>
                            <div class="col-md-6">
                                <div class="form-control">
                                    {{ $doctor_course->batch_shift_history->note ?? '' }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Shifted At</label>
                            <div class="col-md-6">
                                <div class="form-control">
                                    {{ $doctor_course->batch_shift_history && $doctor_course->batch_shift_history->shifted_at ? $doctor_course->batch_shift_history->shifted_at->format("d M Y") : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Shifted By</label>
                            <div class="col-md-6">
                                <div class="form-control">
                                    {{ $doctor_course->batch_shift_history->admin->name ?? '' }}
                                    (<b>{{ $doctor_course->batch_shift_history->admin->phone_number ?? '' }}</b>)
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div id="batch_shifted_info_div" style="display:none;">
                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch Shifting to (*)</label>
                            <div class="col-md-3">
                                <select type="text" name="to_doctor_course_id"  class="form-control" id="to_doctor_course">
                                    <option value="">Select One</option>
                                    @foreach($doctor_course->doctor->doctor_courses as $other_doctor_course)
                                    <option value="{{ $other_doctor_course->id }}">
                                        ({{ $other_doctor_course->reg_no }}) {{ $other_doctor_course->batch->name ?? '' }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Batch Shift Fee (*)</label>
                            <div class="col-md-3">
                                <input type="number" name="shift_fee" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Maintenance Charge (*)</label>
                            <div class="col-md-3">
                                <input type="number" name="service_charge" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-3 control-label">Note (Optional)</label>
                            <div class="col-md-3">
                                <input type="text" name="batch_shifted_info" class="form-control">
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="panel panel-primary" style="border-color: #eee; ">
                <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee;">
                    Course Information
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Year (&#x1F512;)</label>
                        <div class="col-md-3">
                            <div class="form-control">
                                {{ $doctor_course->year ?? '' }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Branch (&#x1F512;)</label>
                        <div class="col-md-3">
                            <div class="form-control">
                                {{ $doctor_course->branch->name ?? '' }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Institute (&#x1F512;)</label>
                        <div class="col-md-3">
                            <div class="form-control">
                                {{ $doctor_course->institute->name ?? '' }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Course (&#x1F512;)</label>
                        <div class="col-md-3">
                            <div class="form-control">
                                {{ $doctor_course->course->name ?? '' }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Sessions (&#x1F512;)</label>
                        <div class="col-md-3">
                            <div class="form-control">
                                {{ $doctor_course->session->name ?? '' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-primary" style="border-color: #eee; ">
            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee;">
                Allow to change Information
            </div>


        <div id="candidateType" class="form-group my-3">
            <label class="col-md-3 control-label">Candidate Type (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
            <div class="col-md-3">
                <select class="form-control" name="candidate_type">
                    <option value="">Select Candidate Type</option>
                    <option  {{ $doctor_course->candidate_type == 'Autonomous/Private' ? 'selected' : ''  }}  value="Autonomous/Private">Autonomous/Private</option>
                    <option {{ $doctor_course->candidate_type == 'Government' ? 'selected' : ''  }} value="Government">Government</option>
                    <option {{ $doctor_course->candidate_type == 'BSMMU' ? 'selected' : ''  }} value="BSMMU">BSMMU</option>
                    <option {{ $doctor_course->candidate_type == 'Armed Forces' ? 'selected' : ''  }} value="Armed Forces">Armed Forces</option>
                    <option {{ $doctor_course->candidate_type == 'Others' ? 'selected' : ''  }} value="Others">Others</option>
                </select>
            </div>
        </div>

        <div class="courses">
          <div class="form-group">
            <label class="col-md-3 control-label">Course (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
            <div class="col-md-3">
              @php  $courses->prepend('Select Course', ''); @endphp
              {!! Form::select('course_id',$courses, isset($doctor_course->course_id) ? $doctor_course->course_id : '',['class'=>'form-control','required'=>'required','id'=>'course_id']) !!}<i></i>
            </div>
           <input type="hidden" name="url" value="{{$url}}">
         </div>
        </div>

        <div class="session-faculty">
            <div class="sessions">
                <div class="form-group mt-3">
                    <label class="col-md-3 control-label">Sessions (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                    <div class="col-md-3">
                        @php  $sessions->prepend('Select Session', ''); @endphp
                        {!! Form::select('session_id',$sessions,isset($doctor_course->session_id) ? $doctor_course->session_id : '' ,['class'=>'form-control','required'=>'required','id'=>'session_id']) !!}<i></i>
                    </div>
                </div>
            </div>
            
            <div class="faculties">
                @if($institute_type==1)
                    <div class="form-group">
                        <label class="col-md-3 control-label">{{ $doctor_course->institute->faculty_label() }} (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                        <div class="col-md-3">
                        @php  $faculties->prepend('Select Faculty', ''); @endphp
                        {!! Form::select('faculty_id',$faculties, isset($doctor_course->faculty_id) ? $doctor_course->faculty_id : '' ,['class'=>'form-control','id'=>'faculty_id']) !!}<i></i>
                        </div>
                    </div>
                @endif
            </div>

        </div>
{{-- 
        <div class="session-subject">


        </div> --}}


{{--           <div class="faculties">--}}
{{--               @if(isset($exam_assign->institute->type))--}}
{{--                   @if($exam_assign->institute->type==1)--}}
{{--                       <div class="form-group">--}}
{{--                           <label class="col-md-3 control-label">{{ $exam_assign->institute->faculty_label() }}</label>--}}
{{--                           <div class="col-md-3">--}}
{{--                               {!! Form::select('faculty_id[]',$faculties, $selected_faculties,['class'=>'form-control  select2 ', 'multiple' => 'multiple','id'=>'faculty_id', 'data-placeholder' => 'Select Faculty']) !!}<i></i>--}}
{{--                           </div>--}}
{{--                           <input type="checkbox" id="checkbox_faculty" > Select All--}}
{{--                       </div>--}}
{{--                   @endif--}}
{{--               @endif--}}
{{--           </div>--}}

{{--           <div class="disciplines">--}}
{{--               <div class="form-group">--}}
{{--                   @if(isset( $exam_assign->institute->type ))--}}
{{--                       @php  $is_combineed = $exam_assign->institute->id == \App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID @endphp--}}

{{--                       @if($exam_assign->institute->type!=1 || $exam_assign->institute->id == \App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID)--}}
{{--                           <label class="col-md-3 control-label">{{ $doctor_course->institute->discipline_label() }}</label>--}}
{{--                           <div class="col-md-3">--}}
{{--                               {!! Form::select('subject_id[]',$subjects, $selected_subjects,['class'=>'form-control  select2 ', 'multiple' => 'multiple','id'=>'subject_id','data-placeholder' => 'Select Subject' ]) !!}<i></i>--}}
{{--                           </div>--}}
{{--                           <input type="checkbox" id="checkbox" > Select All--}}
{{--                       @endif--}}
{{--                   @endif--}}
{{--               </div>--}}
{{--           </div>--}}


        {{-- <div class="faculties">
          @if($institute_type==1)
             <div class="form-group">
                 <label class="col-md-3 control-label">{{ $doctor_course->institute->faculty_label() }} (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
                 <div class="col-md-3">
                  @php  $faculties->prepend('Select Faculty', ''); @endphp
                  {!! Form::select('faculty_id',$faculties, isset($doctor_course->faculty_id) ? $doctor_course->faculty_id : '' ,['class'=>'form-control','id'=>'faculty_id']) !!}<i></i>
                </div>
              </div>
           @endif
        </div> --}}

        <div class="admin-subjects ddd">
         <div class="form-group">
            <label class="col-md-3 control-label">{{ $doctor_course->institute_id == \App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID ? 'Residency':'' }} Discipline (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>)</label>
            @php  $is_combineed = ($doctor_course->institute_id ?? '') == \App\Providers\AppServiceProvider::$COMBINED_INSTITUTE_ID @endphp

           <div class="col-md-3">
             @php  $subjects->prepend('--Select Discipline--', ''); @endphp
             {!! Form::select('subject_id',$subjects, isset($doctor_course->subject_id) ? $doctor_course->subject_id : '' ,['class'=>'form-control','id'=>'subject_id']) !!}<i></i>
           </div>
         </div>
        </div>

       <div class="bcps_subjects">
           @if(isset($bcps_subjects))
             <div class="form-group">
               <label class="col-md-3 control-label">FCPS Part-1 Discipline</label>
               <div class="col-md-3">
                 @php  $bcps_subjects->prepend('Select BCPS Discipline', ''); @endphp
                 {!! Form::select('bcps_subject_id', $bcps_subjects,  old( 'bcps_subject_id', $doctor_course->bcps_subject_id?? '' ) ,
                    ['class'=>'form-control','id'=>'bcps_subject_id']) !!}<i></i>
               </div>
             </div>
           @endif
        </div>

        <div class="batches">
         <div class="form-group">
           <label class="col-md-3 control-label">Batch (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
           <div class="col-md-3">
             @php  $batches->prepend('Select Batch', ''); @endphp
             {!! Form::select('batch_id',$batches, isset($doctor_course->batch_id) ? $doctor_course->batch_id : '' ,['class'=>'form-control batch2','required'=>'required','id'=>'batch_id']) !!}<i></i>
           </div>
         </div>
        </div>


       {{-- @if( ( $doctor_course->batch->is_show_lecture_sheet ?? 0 ) == 1 )
        <div class="" id="lecture_sheet">
            <div class="form-group ">
                <label class="col-md-3 control-label">Lecture Sheet (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                <div class="col-md-3" id="id_div_include_lecture_sheet">
                    <label class="radio-inline">
                        <input type="radio" name="include_lecture_sheet" required value="1" {{  $doctor_course->include_lecture_sheet == "1" ? "checked" : '' }}  > Yes
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="include_lecture_sheet" required  value="0" {{  $doctor_course->include_lecture_sheet == "0" ? "checked" : '' }} > No
                    </label>
                </div>
            </div>
        </div>
       @endif --}}
{{--           change--}}
        @if( ( $doctor_course->batch->is_show_lecture_sheet_fee ?? 'No' ) == 'Yes')

            <div class="" id="lecture_sheet">
                <div class="form-group ">
                    <label class="col-md-3 control-label">Lecture Sheet (<i class="fa fa-asterisk ipd-star" style="font-size:11px;"></i>) </label>
                    <div class="col-md-3" id="id_div_include_lecture_sheet">
                        <label class="radio-inline">
                            <input type="radio" name="include_lecture_sheet" required value="1" {{  $doctor_course->include_lecture_sheet == "1" ? "checked" : '' }}  > Yes
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="include_lecture_sheet" required  value="0" {{  $doctor_course->include_lecture_sheet == "0" ? "checked" : '' }} > No
                        </label>
                    </div>
                </div>
            </div>

            
            <div class="delivery_status">
                    @if($doctor_course->include_lecture_sheet == '1')
                    <div class="form-group">
                        <label class="col-md-3 control-label">Delivery (<i class="fa fa-asterisk ipd-star"style="font-size:11px;"></i>) </label>

                        <div class="col-md-3" id="id_div_lecture_sheet_collection">
                            <label class="radio-inline">
                                <input type="radio" class="home" name="delivery_status" required value="1"
                                        {{  $doctor_course->delivery_status == "1" ? "checked" : '' }}> Courier Address
                            </label>
                            <label class="radio-inline">
                                <input type="radio" class="home" name="delivery_status" required value="0"
                                        {{  $doctor_course->delivery_status == "0" ? "checked" : '' }} > GENESIS Office Collection
                            </label>
                        </div>
                    </div>
                    @endif
            </div>
            
            <div class="courier_division">
                @if($doctor_course->delivery_status == "1")
                <div class="form-group">
                    <label class="col-md-3 control-label">Courier Division (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                    <div class="col-md-3">
                        @php  $divisions->prepend('Select Division', ''); @endphp
                        {!! Form::select('courier_division_id',$divisions, $doctor_course->courier_division_id,['class'=>'form-control','required'=>'required']) !!}<i></i>
                    </div>
                </div>
                @endif
            </div>

            <div class="courier_district">
                @if($doctor_course->delivery_status == "1")
                <div class="form-group">
                    <label class="col-md-3 control-label">Courier District (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                    <div class="col-md-3">
                        @php  $districts->prepend('Select District', ''); @endphp
                        {!! Form::select('courier_district_id',$districts, $doctor_course->courier_district_id,['class'=>'form-control','required'=>'required']) !!}<i></i>
                    </div>
                </div>
                @endif
            </div>

            <div class="courier_upazila">
                @if($doctor_course->delivery_status == "1")
                <div class="form-group">
                    <label class="col-md-3 control-label">Courier Upazila (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                    <div class="col-md-3">
                        @php  $upazilas->prepend('Select Upazila', ''); @endphp
                        {!! Form::select('courier_upazila_id',$upazilas, $doctor_course->courier_upazila_id,['class'=>'form-control','required'=>'required']) !!}<i></i>
                    </div>
                </div>
                @endif
            </div>

            <div class="courier_address">
                @if($doctor_course->delivery_status == "1")
                <div class="form-group">
                    <label class="col-md-3 control-label">Courier Address (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
                    <div class="col-md-3">
                        <div class="input-icon right">
                            <textarea class="form-control" rows="3" name="courier_address" required >{{ $doctor_course->courier_address }}</textarea>
                        </div>
                    </div>
                </div>
                @endif
            </div>
       @endif


        <!-- <div class="reg_no"> -->
         <div class="form-group">
           <label class="col-md-3 control-label">Reg No. (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
           <div class="col-md-3">
             <div class="input-group">
               <span id="reg_no_first_part" class="input-group-addon">{{ isset($doctor_course->reg_no_first_part) ? $doctor_course->reg_no_first_part : '' }}</span>
               <input type="hidden" name="reg_no_first_part" required value="{{ isset($doctor_course->reg_no_first_part) ? $doctor_course->reg_no_first_part : '' }}">
               <input type="text" name="reg_no_last_part" required value="{{ isset($doctor_course->reg_no_last_part) ? $doctor_course->reg_no_last_part : ''}} " class="form-control" placeholder="_ _ _" >
               {{--<input type="text" name="reg_no_last_part" value="" class="form-control" placeholder="_ _ _" minlength="3" maxlength="3">--}}
             </div>
             {{-- <div><span id="range" class="" style="color:green;font-weight:700;">{{ $range }}</span></div> --}}
           </div>
         </div>
         
         <!-- </div> -->
         <div class="form-group">
           <label class="col-md-3 control-label">Institue Roll</label>
           <div class="col-md-3">
               <input type="text" name="roll" value="{{ $doctor_course->roll }}" class="form-control" >
           </div>
         </div>

       </div>
      </div>

      {{-- <div class="lecture_sheets">
        <div class="form-group ">
            <label class="col-md-3 control-label">Payment Status </label>
            <div class="col-md-3">
                <label class="">
                    <input type="checkbox" name="payment_is_completed" value="Completed" {{  $doctor_course->payment_status == "Completed" ? "checked" : '' }}> &nbsp; Is Completed
                </label>
            </div>
        </div>
    </div> --}}

        
      
     <div class="form-group">
         <div class="payment_status">
             <label class="col-md-3 control-label">Payment Status (<i class="fa fa-asterisk ipd-star"style="font-size:11px;"></i>) </label>
             <div class="col-md-3" id="id_div_payment_status">
                 <span class="text text-info" style="font-size:15px;font-weight: 700;">{{ $doctor_course->payment_status }}</span><br>
                 <span class="text text-info" style="font-size:15px;font-weight: 700;">{{ 'Paid amount : '.$doctor_course->paid_amount().' , Course Price : '.$doctor_course->course_price }}</span><br>
                 @if($doctor_course->payment_status == "Completed" && isset($doctor_course->payment_completed_by->name))
                    <span class="text text-info" style="font-size:15px;font-weight: 700;">Completed By : {{ $doctor_course->payment_completed_by->name }}</span>
                 @endif
                 
             </div>
         </div>

         @if($doctor_course->payment_status == "No Payment" || $doctor_course->payment_status == "In Progress" || $doctor_course->payment_status == "")
         <!-- <div class="col-md-1">
            <a class='btn btn-md btn-primary' href="{{ url('admin/doctor-course-payment-form/'.$doctor_course->id) }}">Add Payment</a>
         </div> -->
         @endif
     </div>

        <div class="form-group ">
            <label class="col-md-3 control-label">Discount Code </label>
            <div class="col-md-3">
                <input type="text" class="form-control" name="discount_code" value="{{ $doctor_course->discount_code }}">
            </div>
        </div>

      <div class="form-group">
       <label class="col-md-3 control-label">Status (<i class="fa fa-asterisk ipd-star" style="font-size:9px;"></i>) </label>
       <div class="col-md-3">
        {!! Form::select('status', ['1' => 'Active','0' => 'InActive'], $doctor_course->status,['class'=>'form-control','required'=>'required']) !!}<i></i>
       </div>
      </div>      

                        <div class="panel panel-primary"  style="border-color: #eee; ">
                            <div class="panel-heading" style="background-color: #eee; color: black; border-color: #eee; ">VIP Doctor Information</div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">VIP Doctor</label>
                                    <div class="col-md-9">
                                        <label class="radio-inline">
                                            <input type="radio" name="is_vip" value="1" onchange="checkVIP(this)" required {{ $doctor_course->doctor->is_vip ? 'checked' : '' }}> Yes
                                        </label>
                                        <label class="radio-inline">
                                            <input type="radio" name="is_vip" value="0" onchange="checkVIP(this)" required {{ $doctor_course->doctor->is_vip ? '' : 'checked' }}> No
                                        </label>
                                        <label class="radio-inline" style="width: calc(100% - 110px);">
                                            <input id="VIPDetails" type="text" style="display: {{ $doctor_course->doctor->is_vip ? '' : 'none' }};"  class="form-control" name="vip" value="{{ $doctor_course->doctor->vip ?? '' }}" placeholder="VIP Details">
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
 
     </div>
     <div class="form-actions">
      <div class="form-group">
       <label class="col-md-3 control-label"></label>
       <div class="col-md-3">
        <button type="submit" class="btn btn-info">Update</button>
        <a href="{{ url('admin/doctors-courses') }}" class="btn btn-default">Cancel</a>
       </div>
      </div>
     </div>
    {!! Form::close() !!}
    <!-- END FORM-->
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


    <script type="text/javascript">
        function checkVIP(input) {
            const VIPDetails = document.getElementById('VIPDetails');
            if(input.value === '1') {
                VIPDetails.style.display = '';
                VIPDetails.setAttribute('required', true);
            } else {
                VIPDetails.style.display = 'none';
                VIPDetails.removeAttribute('required');
            }
        }

        document.getElementById('batch_shifted_yes').onclick = () => {
            document.getElementById("batch_shifted_info_div").style.display = "block";
            
            document.getElementById("to_doctor_course").setAttribute('required', true);
            document.getElementById("shifting_price").setAttribute('required', true);
        }

        document.getElementById('batch_shifted_no').onclick = () => {
            document.getElementById("batch_shifted_info_div").style.display = "none";

            document.getElementById("to_doctor_course").removeAttribute('required');
            document.getElementById("shifting_price").removeAttribute('required');
        }

        $(document).ready(function() {

            $('.batch2').select2({});

            $('.payment_status').click(function(e){e.preventDefault();});
            //$('[name="year"]').prop('disabled', true);
            //$('[name="session_id"]').prop('disabled', true);
            //$('[name="branch_id"]').prop('disabled', true);
            //$('[name="institute_id"]').prop('disabled', true);
            //$('[name="course_id"]').prop('disabled', true);
            //$('[name="batch_id"]').prop('disabled', true);
            //$('[name="reg_no_last_part"]').prop('disabled', true);

            $('.doctor2').select2({
                minimumInputLength: 3,
                placeholder: "Please type doctor's name or bmdc no",
                escapeMarkup: function (markup) { return markup; },
                language: {
                    noResults: function () {
                        return "No Doctors found, for add new doctor please <a target='_blank' href='{{ url('admin/doctors/create') }}'>Click here</a>";
                    }
                },
                ajax: {
                    url: '/admin/search-doctors',
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
                                let title = item.name + " - " + (item.bmdc_no || "") + " - " + (item.phone || "");
                                return { id:item.id , text: title };
                            })
                        };
                    }
                }
            });

            $("body").on( "change", "[name='institute_id']", function() {
                var institute_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/institute-courses',
                    dataType: 'HTML',
                    data: {institute_id : institute_id},
                    success: function( data ) {
                        $('.courses').html('');
                        $('.faculties').html('');
                        $('.admin-subjects').html('');
                        $('.batches').html('');
                        $('.batch_details').html('');
                        $('.delivery_status').html('');
                        $('.courier_division').html('');
                        $('.courier_district').html('');
                        $('.courier_upazila').html('');
                        $('.courier_address').html('');
                        $('.lecture_sheets').html('');
                        $('.reg_no').html('');
                        $('#reg_no_first_part').text('');
                        $('[name="reg_no_first_part"]').val('');
                        $('[name="reg_no_last_part"]').val('');
                        $('#range').html('');
                        $('#message').html('');
                        $('.session-faculty').html('');
                        $('.session-subject').html('');
                        $('.courses').html(data);
                    }
                });
            });

            $("body").on( "change", "[name='course_id']", function() {
                var course_id = $("[name='course_id']").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/course-changed',
                    dataType: 'HTML',
                    data: {course_id: course_id},
                    success: function( data ) {
                        $('.faculties').html('');
                        $('.admin-subjects').html('');
                        //$('.batches').html('');
                        $('.delivery_status').html('');
                        $('.courier_division').html('');
                        $('.courier_district').html('');
                        $('.courier_upazila').html('');
                        $('.courier_address').html('');
                        $('.lecture_sheets').html('');
                        $('.reg_no').html('');
                        $('#reg_no_first_part').text('');
                        $('[name="reg_no_first_part"]').val('');
                        $('[name="reg_no_last_part"]').val('');
                        $('#range').html('');
                        $('#message').html('');
                        $('.session-faculty').html('');
                        $('.session-subject').html('');
                        $('.session-faculty').html(data);
                        $('#subject_id').select2();

                    }
                });
            });




            $("body").on( "change", "[name='faculty_id']", function() {
                var faculty_id = $(this).val();
                var institute_id = $("[name='institute_id']").val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/faculty-subjects-in-admission',
                    dataType: 'HTML',
                    data: { faculty_id: faculty_id, institute_id },
                    success: function( data ) {
                        $('.admin-subjects').html(data);
                        $('#subject_id').select2();
                    }
                });
            });

            // $("body").on( "change", "[name='faculty_id']", function() {
            //     var faculty_id = $(this).val();
            //     var is_combined = $('#is_combined').val();
            //     $.ajax({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         type: "POST",
            //         url: '/faculty-subjects-in-admission',
            //         dataType: 'HTML',
            //         data: {faculty_id: faculty_id, is_combined },
            //         success: function( data ) {
            //             $('.subjects').html(data);
            //         }
            //     });
            // });



            // $("body").on( "change", "[name='faculty_id']", function() {
            //     var institute_id = $("[name='institute_id']").val();
            //     var course_id = $("[name='course_id']").val();
            //     var faculty_id = $(this).val();
            //     $.ajax({
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         type: "POST",
            //         url: '/admin/faculty-changed-in-lecture-videos',
            //         dataType: 'HTML',
            //         data: {institute_id:institute_id,course_id:course_id,faculty_id: faculty_id},
            //         success: function( data ) {
            //             var data = JSON.parse(data);
            //             $('.disciplines').html('');
            //             $('.disciplines').html(data['subjects']);
            //             $('.select2').select2({ });
            //         }
            //     });
            // })


            $("body").on( "change", "[name='branch_id'],[name='course_id'],[name='subject_id'],[name='faculty_id'],[name='session_id']", function() {

                var institute_id = $("[name='institute_id']").val();
                var course_id = $("[name='course_id']").val();
                var session_id = $("[name='session_id']").val();
                var branch_id = $("[name='branch_id']").val();
                var subject_id = $("[name='subject_id']").val();
                var faculty_id = $("[name='faculty_id']").val();
                var is_combined = $("#is_combined").val();
// console.log(institute_id,session_id,branch_id,subject_id,faculty_id);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/courses-branches-batches',
                    dataType: 'HTML',
                    data: { institute_id : institute_id, course_id : course_id , branch_id : branch_id ,faculty_id :faculty_id ,subject_id :subject_id, session_id :session_id, is_combined },
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.batch_details').html('');
                        $('.lecture_sheets').html('');
                        $('.batches').html(data['batches']);
                        $('#batch_id').select2();
                    }
                });

                });

            $("body").on( "change", "[name='batch_id'],[name='session_id'],[name='year']", function() {

                var year = $('#year').val();
                var session_id = $('#session_id').val();
                var course_id = $('#course_id').val();
                var batch_id = $('#batch_id').val();
                var batch_id = $('#batch_id').val();
                var second_ajax_call = true;

                if(year && session_id && course_id && batch_id) {
                    second_ajax_call = false;
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/admin/registration-no',
                        dataType: 'HTML',
                        data: {year: year, session_id: session_id, course_id: course_id, batch_id: batch_id},
                        success: function (data) {
                            var data = JSON.parse(data);
                            $('.delivery_status').html('');
                            $('.courier_division').html('');
                            $('.courier_district').html('');
                            $('.courier_upazila').html('');
                            $('.courier_address').html('');
                            $('.lecture_sheets').html('');
                            $('.lecture_sheets').html(data['lecture_sheets']);
                            $('.batch_details').html('');
                            $('.batch_details').html(data['batch_details']);
                            $('#range').html('');
                            $('#message').html('');
                            $('#reg_no_first_part').text(data['reg_no_first_part']);
                            $('[name="reg_no_first_part"]').val(data['reg_no_first_part']);
                            $('[name="reg_no_last_part"]').val(data['reg_no_last_part']);
                            $('[name="t_id"]').val(data['t_id']);
                            // $('#range').html(data['range']);
                            $('#message').html(data['message']);
                            $('#submit').prop( "disabled", false );
                            if(data['message'] !== null && data['message'] !== '')
                            {
                                $('#submit').prop( "disabled", true );
                            }

                            if(data['is_lecture_sheet'] == 'Yes')
                            {
                                $('#lecture_sheet').css({ display: "block" });
                                $('[name="include_lecture_sheet"]').attr("required", true);
                            }
                            if(data['is_lecture_sheet'] == 'No')
                            {
                                $('#lecture_sheet').css({ display: "none" });
                                $('[name="include_lecture_sheet"]').attr("required", false);
                            }

                        }
                    });

                }

                if(second_ajax_call && course_id && batch_id) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/admin/batch-details',
                        dataType: 'HTML',
                        data: {course_id: course_id, batch_id: batch_id},
                        success: function (data) {
                            var data = JSON.parse(data);
                            $('.batch_details').html('');
                            $('.batch_details').html(data['batch_details']);

                        }
                    });
                }
            });

            $("body").on("change", "[name='include_lecture_sheet']", function () {
                var include_lecture_sheet = $(this).val();
                if(include_lecture_sheet == '1')
                {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/admin/change-include-lecture-sheet',
                        dataType: 'HTML',
                        data: {include_lecture_sheet: include_lecture_sheet},
                        success: function( data ) {

                            $('.delivery_status').html(data);
                        }
                    });

                }
                else
                {
                    $('.delivery_status').html('');
                    $('.courier_division').html('');
                    $('.courier_district').html('');
                    $('.courier_upazila').html('');
                    $('.courier_address').html('');
                }

            });

            $("body").on("change", "[name='delivery_status']", function () {
                var delivery_status = $(this).val();
                if(delivery_status == '1')
                {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '/admin/change-lecture-sheet-collection',
                        dataType: 'HTML',
                        data: {delivery_status: delivery_status},
                        success: function( data ) {
                            $('.courier_division').html(data);
                            $('.courier_district').html('');
                            $('.courier_upazila').html('');
                            $('.courier_address').html('');
                        }
                    });

                }
                else
                {
                    $('.courier_division').html('');
                    $('.courier_district').html('');
                    $('.courier_upazila').html('');
                    $('.courier_address').html('');
                }

            });

            $("body").on( "change", "[name='courier_division_id']", function() {
                var courier_division_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/courier-division-district',
                    dataType: 'HTML',
                    data: {courier_division_id: courier_division_id},
                    success: function( data ) {
                        $('.courier_district').html(data);
                        $('.courier_upazila').html('');
                        $('.courier_address').html('');
                    }
                });
            });

            $("body").on( "change", "[name='courier_district_id']", function() {
                var courier_district_id = $(this).val();
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    url: '/admin/courier-district-upazila',
                    dataType: 'HTML',
                    data: {courier_district_id: courier_district_id},
                    success: function( data ) {
                        var data = JSON.parse(data);
                        $('.courier_upazila').html(data['upazilas']);
                        $('.courier_address').html(data['courier_address']);
                    }
                });
            });

            $('#subject_id').select2();
            // $('#to_doctor_course').select2();


        })
    </script>


@endsection
