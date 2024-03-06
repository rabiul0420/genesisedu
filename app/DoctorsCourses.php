<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Builder\Function_;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Str;

class DoctorsCourses extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $table = 'doctors_courses';

    public function session()
    {
        return $this->belongsTo('App\Sessions','session_id','id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Branch::class,'branch_id','id');
    }
    
    public function service_packages()
    {
        return $this->belongsTo('App\ServicePackages','service_package_id','id');
    }

    public function coming_by()
    {
        return $this->belongsTo('App\ComingBy','coming_by_id','id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctors::class,'doctor_id','id');
    }

    public function institute()
    {
        return $this->belongsTo('App\Institutes','institute_id','id');
    }

    public function course()
    {
        return $this->belongsTo('App\Courses','course_id','id');
    }

    public function faculty()
    {
        return $this->belongsTo('App\Faculty','faculty_id','id');
    }

    public function batch()
    {
        return $this->belongsTo(Batches::class,'batch_id','id');
    }

    public function active_batch()
    {
        return $this->belongsTo(Batches::class,'batch_id','id');
    }

    public function subject()
    {
        return $this->belongsTo('App\Subjects','subject_id','id');
    }

    public function bcps_subject( )
    {
        return $this->belongsTo('App\Subjects','bcps_subject_id','id');
    }

    public function online_exam_links()
    {
        return $this->hasMany('App\OnlineExamLink','subject_id','id');
    }

    public function lecture_sheets()
    {
        return $this->hasMany('App\DoctorCourseLectureSheet','doctor_course_id','id');
    }

    public function transactions()
    {
        return $this->hasMany( DoctorCoursePayment::class,'doctor_course_id','id');
    }

    public function courier_upazila()
    {
        return $this->belongsTo('App\Upazilas','courier_upazila_id','id');
    }

    public function courier_district()
    {
        return $this->belongsTo('App\Districts','courier_district_id','id');
    }

    public function courier_division()
    {
        return $this->belongsTo('App\Divisions','courier_division_id','id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','created_by','id');
    }

  
    public function courier()
    {
        return $this->belongsTo('App\Courier','courier_id','id');
    }

    public function payment_completed_by()
    {
        return $this->belongsTo('App\User','payment_completed_by_id','id');

    }

    public function site_setup()
    {
        return $this->belongsTo('App\SiteSetup','course_id','course_id');

    }

    public function lecturesheet()
    {
        return $this->hasMany('App\LectureSheetDeliveryStatus','doctor_course_id','id');
    }

    public function doctor_exams()
    {
        return $this->hasMany(DoctorExam::class, 'doctor_course_id');
    }
    public function course_inactive(){
        return $this->status == 1? "Active":"InActive";
    }

    public function batch_shifted(){
        return $this->batch_shifted==1? "Yes":"No";
    }

    public function batch_shift_history()
    {
        return $this->hasOne(BatchShift::class, 'from_doctor_course_id');
    }

    public function batch_shift_from()
    {
        return $this->hasOne(BatchShift::class, 'to_doctor_course_id');
    }

    public function payment_options()
    {
        return $this->hasMany(DoctorCoursePaymentOptions::class, 'doctor_course_id','id');
    }

    public function readonly()
    {
        $readonly = '';

        if($this->payment_status == "Completed")
        {            
            $readonly = 'readonly';            
        }
        else if($this->payment_status != "Completed")
        {
            if($this->batch->payment_times > 1)
            {
                if($this->payment_option == '' || $this->payment_option == 'single' )
                {
                    $readonly = 'readonly';
                }

            }
            else
            {
                $readonly = 'readonly';
            }            
                        
        }        
        
        return $readonly;

    }

    public function get_payment_info()
    {
        $paid_amount = $this->paid_amount();

        $payment_info = array('paid_amount'=>$paid_amount,'previous_installment'=>'0','next_installment'=>'0','total_payable'=>$this->course_price,'readonly'=>'');

        if($this->payment_status == "Completed")
        {
            $payment_info['next_installment'] = 0;
            
            $payment_info['readonly'] = 'readonly';
            
        }
        else if($this->payment_status != "Completed")
        {
            if($this->batch->payment_times > 1)
            {
                if($this->payment_option == "custom")
                {
                    $doctor_course_payment_options = DoctorCoursePaymentOptions::where(['doctor_course_id'=>$this->id])->get();
                    if(isset($doctor_course_payment_options))
                    {
                        $today_date = Date('Ymd',time());$j=0;
                        foreach($doctor_course_payment_options as $payment_option)
                        {
                            $payment_option_date = Date('Ymd',date_create_from_format("Y-m-d",$payment_option->payment_date)->getTimestamp());
                            if($payment_option_date >= $today_date && $paid_amount < $payment_option->amount)
                            {
                                //$payment_info['previous_installment'] = $doctor_course_payment_options[$j-1];
                                $payment_info['next_installment'] = $payment_option;break; 
                            }
                            $j++;                            
                        }
                        
                    }

                }
                else if($this->payment_option == "default")
                {
                    $doctor_course_payment_options = BatchPaymentOptions::where(['batch_id'=>$this->batch->id])->get();
                    if(isset($doctor_course_payment_options))
                    {
                        $today_date = Date('Ymd',time());$j=0;
                        foreach($doctor_course_payment_options as $payment_option)
                        {
                            $payment_option_date = Date('Ymd',date_create_from_format("Y-m-d",$payment_option->payment_date)->getTimestamp());
                            if($payment_option_date >= $today_date && $paid_amount < $payment_option->amount)
                            {
                                //$payment_info['previous_installment'] = $doctor_course_payment_options[$j-1];
                                $payment_info['next_installment'] = $payment_option;break; 
                            }
                            $j++; 
                            
                        }
                        
                    }

                }

            }
            else
            {
                $payment_info['readonly'] = 'readonly';
            }
            
                        
        }        
        
        return $payment_info;

    }

    public function eligibility()
    {

        $paid_amount = $payable_amount = 0;
        $paid_amount = $this->paid_amount();
        
        if($this->batch->payment_times > 1)
        {
            
            if($this->payment_option == "custom")
            {
                $doctor_course_payment_options = DoctorCoursePaymentOptions::where(['doctor_course_id'=>$this->id])->get();
                if(isset($doctor_course_payment_options))
                {
                    $today_date = Date('Ymd',time());
                    foreach($doctor_course_payment_options as $payment_option)
                    {
                        
                        $payment_option_date = Date('Ymd',date_create_from_format("Y-m-d",$payment_option->payment_date)->getTimestamp());
                        if($payment_option_date <= $today_date)
                        {
                            $payable_amount = $payment_option->amount; 
                        }
                        else
                        {
                            break;
                        }
                        
                    }
                    
                }

            }
            else if($this->payment_option == "default")
            {
                $doctor_course_payment_options = BatchPaymentOptions::where(['batch_id'=>$this->batch->id])->get();
                if(isset($doctor_course_payment_options))
                {
                    $today_date = Date('Ymd',time());
                    foreach($doctor_course_payment_options as $payment_option)
                    {
                        
                        $payment_option_date = Date('Ymd',date_create_from_format("Y-m-d",$payment_option->payment_date)->getTimestamp());
                        if($payment_option_date <= $today_date)
                        {
                            $payable_amount = $payment_option->amount; 
                        }
                        else
                        {
                            break;
                        }
                        
                    }
                    
                }

            }
            else if($this->payment_option == "single" || $this->payment_option == '')
            {
                if($paid_amount >= $this->course_price)
                {
                    return true;
                }
                else
                {
                    return false;
                }

            }

            $payable_amount = ($payable_amount*$this->course_price)/100;
            
            if($paid_amount >= $payable_amount)
            {
                return true;
            }
            else
            {
                return false;
            }
             
        }
        else
        {
            if($this->payment_status == "Completed")
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        return false;
    }

    public function set_payment_statuss()
    {
        $paid_amount = 0;
        $paid_amount = $this->paid_amount();

        if($paid_amount == 0)
        {
            $this->payment_status = "Not Completed";
        }
        else if($paid_amount < $this->course_price)
        {
            $this->payment_status = "In Progress";
        }
        else if($paid_amount >= $this->course_price)
        {
            $this->payment_status = "Completed";
        }
        

    }

    public function set_payment_status()
    {
        if(!$this->batch_shift_from && $this->batch->apply_new_discount_rule == 'yes' && $this->discount_type_of_prev_admission() != "No_discount")
        {
            if($this->payment_status == "No Payment")
            {
                Discount::where(['doctor_id'=>$this->doctor_id,'batch_id'=>$this->batch_id,'used'=>'1','status'=>'1','auto_generated'=>'yes'])->delete();
            }
            
            $total_minimum_discount_receivable = $this->total_minimum_discount_recievable();
            $total_discount_from_options = $this->total_discount_from_options();
            //echo "<pre>";print_r($total_minimum_discount_receivable.'-'.$total_discount_from_options);exit;
            if($total_discount_from_options < $total_minimum_discount_receivable )
            {
                $this->discount_adjusment($total_minimum_discount_receivable - $total_discount_from_options);
            }
        }

        if($this->batch_shift_from) {
            $discount_from_service_point = 0;
            $discount_from_prev_admission = 0;
            $full_payment_waiver = 0;
        } else {
            $discount_from_service_point = $this->service_point_discount();
            $discount_from_prev_admission = $this->discount_from_prev_admission();
            $full_payment_waiver = $this->full_payment_waiver();
        }

        $lecture_sheet_fee = $this->lecture_sheet_fee();

        $discount = $this->get_discount($this->discount_code);

        $courier_charge = $this->courier_charge();

        $actual_course_price = $this->actual_course_fee();
        
        $this->actual_course_price = $actual_course_price;

        $this->course_price = $actual_course_price + $lecture_sheet_fee + $courier_charge - $discount_from_prev_admission - $discount - $full_payment_waiver - $discount_from_service_point;
        
        if($this->batch_shift_from) {
        
            $this->course_price += ($this->batch_shift_from->shift_fee ?? 0); // add shifting fee

            $this->course_price += ($this->batch_shift_from->service_charge ?? 0); // add maintenance fee
            
            $this->course_price += ($this->batch_shift_from->payment_adjustment ?? 0); // add adjustment fee

            $prev_doctor_course = $this->batch_shift_from->from_doctor_course ?? null;

            $this->course_price -= ($prev_doctor_course ? $prev_doctor_course->paid_amount() - $prev_doctor_course->lecture_sheet_fee() - $prev_doctor_course->courier_charge() : 0); // adjust prev batch paid amount without lecture sheet
        }

        $this->push();

        $paid_amount = 0;
        $paid_amount = $this->paid_amount();

        if($paid_amount == 0 && $this->course_price == 0)
        {
            $this->payment_status = "Completed";
        }
        else if($paid_amount == 0)
        {
            $this->payment_status = "No Payment";
        }
        else if($paid_amount < $this->course_price)
        {
            $this->payment_status = "In Progress";
        }
        else if(($paid_amount >= $this->course_price))
        {
            $this->payment_status = "Completed";
        }

        $this->push();
    }

    public function discount_adjusment($amount)
    {
        return Discount::insert(['doctor_id'=>$this->doctor_id,'batch_id'=>$this->batch_id,'amount'=>$amount,'discount_code'=>$this->generateCouponCode(),'created_by'=>'99','used'=>'1','code_duration'=>'1','status'=>'1','auto_generated'=>'yes']);
    }

    public function generateCouponCode(){
        $code = strtoupper( Str::random(6));
        if( Discount::where( 'discount_code', $code )->exists() ) {
            return $this->generateCouponCode();
        }
        return $code;
    }

    public function total_discount_from_options()
    {
        //$discount_from_service_point = $this->service_point_discount();
        $discount_from_prev_admission = $this->discount_from_prev_admission();
        //$full_payment_waiver = 0 ; // $this->full_payment_waiver();
        $discount = $this->get_discount($this->discount_code);

        //return $discount_from_service_point + $discount_from_prev_admission + $full_payment_waiver + $discount;
        return $discount_from_prev_admission + $discount;
    }

    public function actual_total_payable()
    {
        $actual_course_price = $this->actual_course_fee();

        return $actual_course_price;
    }

    public function total_minimum_discount_recievable()
    {
        $total_minimum_discount_receivable = 0;
        $actual_total_payable = $this->actual_total_payable();
        $discount_type_of_previous_admission = $this->discount_type_of_prev_admission();
        if($discount_type_of_previous_admission == "Regular")
        {
            if($this->batch->batch_type == "Regular" || $this->batch->batch_type == "Exam")
            {
                $total_minimum_discount_receivable = (int)($actual_total_payable * 25/100);
            }            
        }
        else if($discount_type_of_previous_admission == "Exam")
        {
            if($this->batch->batch_type == "Regular")
            {
                $total_minimum_discount_receivable = (int)($actual_total_payable * 25/100);
            }
            else if($this->batch->batch_type == "Exam")
            {
                $total_minimum_discount_receivable = (int)($actual_total_payable * 15/100);
            }
        }
        else if($discount_type_of_previous_admission == "No_discount")
        {
            $total_minimum_discount_receivable = 0;
        }

        return $total_minimum_discount_receivable;
    }

    public function service_point_discount()
    {
        if($this->batch->service_point_discount == "yes" &&  ( $this->batch->payment_times == 1 || $this->payment_option == "single" ))
        {
            return $this->batch->branch->service_point_discount ?? 0;
        }
        else return 0;
    }

    public function full_payment_waiver()
    {
        if($this->payment_option == "single" && $this->batch->payment_times > 1)
        {
            return $this->batch->full_payment_waiver;
        }
        else return 0;
    }

    public function get_payment_status()
    {
        $paid_amount = 0;
        $paid_amount = $this->paid_amount();

        if($paid_amount == 0)
        {
            $payment_status = "No Payment";
        }
        else if($paid_amount < $this->course_price)
        {
            $payment_status = "In Progress";
        }
        else if($paid_amount >= $this->course_price)
        {
            $payment_status = "Completed";
        }

        return $payment_status;
    }

    public function calculate_core_prices()
    {
        $array_core_price = array('has_error'=>'','actual_course_price'=>'0','discounted_course_price'=>'0','lecture_sheet_fee'=>'0');
        $course_price = $actual_course_price = 0;
        $doctor_selected_batch = $this->batch;
        
        if(isset($doctor_selected_batch->fee_type)==false)
        {
            $array_core_price['has_error'] = "batch_admission_fee_not_set";
        }

        if($doctor_selected_batch->fee_type == "Batch")
        {

            if($this->include_lecture_sheet)
            {

                if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_regular + $doctor_selected_batch->lecture_sheet_fee;
                }
                else if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_exam + $doctor_selected_batch->lecture_sheet_fee;
                }
                else
                {
                    $course_price = $doctor_selected_batch->admission_fee + $doctor_selected_batch->lecture_sheet_fee;
                }

            }
            else
            {
                if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_regular;
                }
                else if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $course_price = $doctor_selected_batch->admission_fee - $doctor_selected_batch->discount_from_exam;
                }
                else
                {
                    $course_price = $doctor_selected_batch->admission_fee;
                }

            }

            $actual_course_price = $doctor_selected_batch->admission_fee;

        }
        else if($doctor_selected_batch->fee_type == "Discipline_Or_Faculty")
        {
            //echo $doctor_selected_batch->institute->type;exit;
            if($doctor_selected_batch->institute->type == 1)
            {

                $doctor_selected_batch_faculty = BatchFacultyFee::where(['batch_id'=>$this->batch_id,'faculty_id'=>$this->faculty_id])->first();
                if(isset($doctor_selected_batch_faculty->admission_fee)==false)
                {
                    $array_core_price['has_error'] = "faculty_admission_fee_not_set";
                }

                if($this->include_lecture_sheet)
                {
                    if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_regular + $doctor_selected_batch_faculty->lecture_sheet_fee;
                    }
                    else if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_exam + $doctor_selected_batch_faculty->lecture_sheet_fee;
                    }
                    else
                    {
                        $course_price = $doctor_selected_batch_faculty->admission_fee + $doctor_selected_batch_faculty->lecture_sheet_fee;

                    }
                }
                else
                {
                    if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_regular;
                    }
                    else if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $course_price = $doctor_selected_batch_faculty->admission_fee - $doctor_selected_batch_faculty->discount_from_exam;
                    }
                    else
                    {
                        $course_price = $doctor_selected_batch_faculty->admission_fee;

                    }

                }

                $actual_course_price = $doctor_selected_batch_faculty->admission_fee;

            }
            else
            {

                $doctor_selected_batch_discipline = BatchDisciplineFee::where(['batch_id'=>$this->batch_id,'subject_id'=>$this->subject_id])->first();
                if(isset($doctor_selected_batch_discipline->admission_fee)==false)
                {
                    $array_core_price['has_error'] = "discipline_admission_fee_not_set";
                }

                if($this->include_lecture_sheet)
                {
                    if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_regular +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                    }
                    else if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_exam +  $doctor_selected_batch_discipline->lecture_sheet_fee;
                    }
                    else
                    {
                        $course_price = $doctor_selected_batch_discipline->admission_fee +  $doctor_selected_batch_discipline->lecture_sheet_fee;

                    }
                }
                else
                {
                    if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_regular;
                    }
                    else if(DoctorsCourses::where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('course_price', '!=' , '0')->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $course_price = $doctor_selected_batch_discipline->admission_fee - $doctor_selected_batch_discipline->discount_from_exam;
                    }
                    else
                    {
                        $course_price = $doctor_selected_batch_discipline->admission_fee;

                    }

                }

                $actual_course_price = $doctor_selected_batch_discipline->admission_fee;
            }

        }

        $array_core_price['actual_course_price'] = $actual_course_price;
        $array_core_price['discounted_course_price'] = $course_price;
        return $array_core_price;

    }

    public function apply_discount_code($discount_code)
    {
        $return_value = false;
        $doctor_discounts = Discount::where(['doctor_id'=>$this->doctor_id,'batch_id'=>$this->batch_id,'status'=>'1'])->get();
        if(isset($doctor_discounts))
        {
            foreach($doctor_discounts as $doctor_discount)
            {   
                if($doctor_discount->used == 0)
                {
                    $diff = time() - $doctor_discount->created_at->timestamp;
                    if((int)($diff/3600) <= (int)$doctor_discount->code_duration && $doctor_discount->discount_code == $discount_code)
                    {
                        $doctor_discount->used = 1;
                        $doctor_discount->push();
                        $return_value = true;break;               
                    }

                }              

            }
        }
        
        return $return_value;
        
    }

    public function get_discount($discount_code)
    {
        $discount = 0;

        $doctor_discounts = Discount::query()
            ->where([
                'doctor_id' => $this->doctor_id,
                'batch_id'  => $this->batch_id,
                'status'    => '1'
            ])
            ->when($this->batch_shift_from, function ($query) {
                $query->whereNull('auto_generated');
            })
            ->get();

        if(isset($doctor_discounts))
        {
            foreach($doctor_discounts as $doctor_discount)
            {
                if($doctor_discount->used == 1)
                {
                    $discount += $doctor_discount->amount;
                }
                else if($doctor_discount->used == 0)
                {
                    $diff = time() - $doctor_discount->created_at->timestamp;
                    if((int)($diff/3600) <= (int)$doctor_discount->code_duration && $doctor_discount->discount_code == $discount_code)
                    {
                        $doctor_discount->used = 1;
                        $doctor_discount->push();
                        $discount += $doctor_discount->amount;                
                    }

                }               

            }
        }

        return $discount;
        
    }

    public function total_discount()
    {
        $discount = 0;
        $discount_from_prev_admission = $this->discount_from_prev_admission();
        $discounts = $this->get_discount($this->discount_code);
        
        return $discount_from_prev_admission + $discounts;
        
    }

    public function discount_codes()
    {
        $doctor_discounts = Discount::query()
            ->where([
                'doctor_id' => $this->doctor_id,
                'batch_id'  => $this->batch_id,
                'used'      => '1',
                'status'    => '1',
            ])
            ->when($this->batch_shift_from, function ($query) {
                $query->whereNull('auto_generated');
            })
            ->get();

        if(isset($doctor_discounts))
        {
            return $doctor_discounts;
        }
        else return '';
        
    }

    public function courier_charge()
    {
        if($this->include_lecture_sheet && $this->delivery_status == 1 && $this->courier_upazila_id == 493)
        {
            return $this->batch->courier_package_charge->inside_dhaka??'0';
        }
        else if($this->include_lecture_sheet &&  $this->delivery_status == 1 && $this->courier_upazila_id != 493)
        {
            return $this->batch->courier_package_charge->outside_dhaka??'0';
        }
        
    }

    public function paid_amount()
    {
        $paid_amount = 0;
        $doctor_course_payments = DoctorCoursePayment::where(['doctor_course_id'=>$this->id])->get();
        if(isset($doctor_course_payments))
        {
            foreach($doctor_course_payments as $doctor_course_payment)
            {
                $paid_amount += $doctor_course_payment->amount;
            }
            
        }

        return $paid_amount;
        
    }

    public function lecture_sheet_fee()
    {

        $lecture_sheet_fee = 0 ;
        $doctor_selected_batch = $this->batch;

        if($doctor_selected_batch->fee_type == "Batch")
        {

            if($this->include_lecture_sheet)
            {
                $lecture_sheet_fee += $doctor_selected_batch->lecture_sheet_fee;
            }
            else 
            {
                $lecture_sheet_fee += 0;
            }

        }
        else if($doctor_selected_batch->fee_type == "Discipline_Or_Faculty")
        {
            //echo $doctor_selected_batch->institute->type;exit;
            if($doctor_selected_batch->institute->type == 1)
            {

                $doctor_selected_batch_faculty = BatchFacultyFee::where(['batch_id'=>$this->batch_id,'faculty_id'=>$this->faculty_id])->first();
                
                if($this->include_lecture_sheet && isset($doctor_selected_batch_faculty))
                {
                    $lecture_sheet_fee += $doctor_selected_batch_faculty->lecture_sheet_fee;
                }
                else 
                {
                    $lecture_sheet_fee += 0;
                }

            }
            else
            {

                $doctor_selected_batch_discipline = BatchDisciplineFee::where(['batch_id'=>$this->batch_id,'subject_id'=>$this->subject_id])->first();
                if($this->include_lecture_sheet && isset($doctor_selected_batch_discipline))
                {
                    $lecture_sheet_fee += $doctor_selected_batch_discipline->lecture_sheet_fee;
                }
                else 
                {
                    $lecture_sheet_fee += 0;
                }

            }

        }

        return $lecture_sheet_fee;

    }

    public function discount_type_of_prev_admission()
    {
        
        $doctor_selected_batch = $this->batch;
        
        if(isset($doctor_selected_batch))
        {
            if($doctor_selected_batch->fee_type == "Batch")
            {   
                
                if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    return "Regular";
                }
                else if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    return "Exam";
                }
                else
                {
                    return "No_discount";
                }

            }
            else if($doctor_selected_batch->fee_type == "Discipline_Or_Faculty")
            {
                
                if($doctor_selected_batch->institute->type == 1)
                {

                    $doctor_selected_batch_faculty = BatchFacultyFee::where(['batch_id'=>$this->batch_id,'faculty_id'=>$this->faculty_id])->first();
                    if(isset($doctor_selected_batch_faculty->admission_fee)==false)
                    {
                        $array_core_price['has_error'] = "faculty_admission_fee_not_set";
                    }

                    if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        return "Regular";
                    }
                    else if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        return "Exam";
                    }
                    else
                    {
                        return "No_discount";
                    }

                }
                else
                {

                    $doctor_selected_batch_discipline = BatchDisciplineFee::where(['batch_id'=>$this->batch_id,'subject_id'=>$this->subject_id])->first();
                    if(isset($doctor_selected_batch_discipline->admission_fee)==false)
                    {
                        $array_core_price['has_error'] = "discipline_admission_fee_not_set";
                    }
                                        
                    if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {   
                        return "Regular";
                    }
                    else if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        return "Exam";
                    }
                    else
                    {
                        return "No_discount";
                    }
                }

            }
        }
        else return "no_discount";

    }

    public function discount_from_prev_admission()
    {
        
        $discount_from_prev_admission = 0;
        
        $doctor_selected_batch = $this->batch;
        
        if(isset($doctor_selected_batch))
        {
            if($doctor_selected_batch->fee_type == "Batch")
            {   
                
                if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $discount_from_prev_admission = $doctor_selected_batch->discount_from_regular;
                }
                else if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                {
                    $discount_from_prev_admission = $doctor_selected_batch->discount_from_exam;
                }
                else
                {
                    $discount_from_prev_admission = 0;
                }

            }
            else if($doctor_selected_batch->fee_type == "Discipline_Or_Faculty")
            {
                
                if($doctor_selected_batch->institute->type == 1)
                {

                    $doctor_selected_batch_faculty = BatchFacultyFee::where(['batch_id'=>$this->batch_id,'faculty_id'=>$this->faculty_id])->first();
                    if(isset($doctor_selected_batch_faculty->admission_fee)==false)
                    {
                        $array_core_price['has_error'] = "faculty_admission_fee_not_set";
                    }

                    if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $discount_from_prev_admission =  $doctor_selected_batch_faculty->discount_from_regular ?? 0;
                    }
                    else if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $discount_from_prev_admission =  $doctor_selected_batch_faculty->discount_from_exam ?? 0;
                    }
                    else
                    {
                        $discount_from_prev_admission = 0;
                    }

                }
                else
                {

                    $doctor_selected_batch_discipline = BatchDisciplineFee::where(['batch_id'=>$this->batch_id,'subject_id'=>$this->subject_id])->first();
                    if(isset($doctor_selected_batch_discipline->admission_fee)==false)
                    {
                        $array_core_price['has_error'] = "discipline_admission_fee_not_set";
                    }
                                        
                    if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Regular','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {   
                        $discount_from_prev_admission = $doctor_selected_batch_discipline->discount_from_regular ?? 0;
                    }
                    else if(DoctorsCourses::select('doctors_courses.*','batches.batch_type')->where(['doctor_id'=>$this->doctor_id,'batches.batch_type'=>'Exam','payment_status'=>'Completed','is_trash' => '0'])->where('doctors_courses.id','!=',$this->id)->where('course_price', '!=' , '0')->where('doctors_courses.created_at','<=',$this->created_at->toDateString())->join('batches','doctors_courses.batch_id','=','batches.id')->first())
                    {
                        $discount_from_prev_admission = $doctor_selected_batch_discipline->discount_from_exam ?? 0;
                    }
                    else
                    {
                        $discount_from_prev_admission = 0;
                    }
                }

            }
        }
        else $discount_from_prev_admission = 0;

        

        return $discount_from_prev_admission;

    }

    public function actual_course_fee()
    {
        $admission_fee = 0;
        
        $doctor_selected_batch = $this->batch;
        
        if(isset($doctor_selected_batch))
        {
            if($doctor_selected_batch->fee_type == "Batch")
            {
                if(isset($doctor_selected_batch))
                $admission_fee = $doctor_selected_batch->admission_fee;
            }
            else if($doctor_selected_batch->fee_type == "Discipline_Or_Faculty")
            {
                //echo $doctor_selected_batch->institute->type;exit;
                if($doctor_selected_batch->institute->type == 1)
                {

                    $doctor_selected_batch_faculty = BatchFacultyFee::where(['batch_id'=>$this->batch_id,'faculty_id'=>$this->faculty_id])->first();
                    if(isset($doctor_selected_batch_faculty))
                    $admission_fee = $doctor_selected_batch_faculty->admission_fee;

                }
                else
                {

                    $doctor_selected_batch_discipline = BatchDisciplineFee::where(['batch_id'=>$this->batch_id,'subject_id'=>$this->subject_id])->first();
                    if(isset($doctor_selected_batch_discipline))
                    $admission_fee = $doctor_selected_batch_discipline->admission_fee;
                }

            }
        }
        else $admission_fee = 0;

        

        return $admission_fee;

    }

    public function payments()
    {        
        $doctor_course_payments = DoctorCoursePayment::where(['doctor_course_id'=>$this->id])->get();
        if(isset($doctor_course_payments) && count($doctor_course_payments))
        {
            return $doctor_course_payments;
        }
        else
        {
            return null;
        }
    }

    public function installments()
    {
        if($this->batch->payment_times > 1)
        {
            if($this->payment_option == "custom")
            {
                return DoctorCoursePaymentOptions::where(['doctor_course_id'=>$this->id])->get();
            }
            else if($this->payment_option == "default")
            {
                return BatchPaymentOptions::where(['batch_id'=>$this->batch->id])->get();
            }
            else if($this->payment_option == "single" || $this->payment_option == '')
            {
                return null;
            }

        }
        else
        {
            return null;
        }               
    }
    
    public function installment_gap($from,$to)
    {
        $from_amount = $to_amount = 0;
        if($this->batch->payment_times > 1)
        {
            if($this->payment_option == "custom")
            {
                $installments =  DoctorCoursePaymentOptions::where(['doctor_course_id'=>$this->id])->get();
                if(isset($installments) && count($installments))
                {
                    foreach($installments as $k=>$installment)
                    {
                        if($k == $from)$from_amount = ($installment->amount * $this->course_price)/100;
                        if($k == $to)$to_amount = ($installment->amount * $this->course_price)/100;
                    }
                }
                return $to_amount - $from_amount;
            }
            else if($this->payment_option == "default")
            {

                $installments =  BatchPaymentOptions::where(['batch_id'=>$this->batch->id])->get();
                if(isset($installments) && count($installments))
                {
                    foreach($installments as $k=>$installment)
                    {
                        if($k == $from)$from_amount = ($installment->amount * $this->course_price)/100;
                        if($k == $to)$to_amount = ($installment->amount * $this->course_price)/100;
                    }
                }
                return $to_amount - $from_amount;
                
            }
            else if($this->payment_option == "single" || $this->payment_option == '')
            {
                return 0;
            }

        }
        else
        {
            return 0;
        }               
    }

    public function check_paid_installment($installment_no)
    {
        $doctor_course_payments = DoctorCoursePayment::where(['doctor_course_id'=>$this->id])->get();
        if(isset($doctor_course_payments) && count($doctor_course_payments))
        {
            $paid_amount = $this->paid_amount();
            $installments = $this->installments();
            if(count($installments))
            {
                foreach($installments as $k=>$installment)
                {
                    if($paid_amount >= ($installment->amount*$this->course_price)/100 && $installment_no == $k)
                    {
                        return true;
                    }
                    
                }
                return false;
            }
            
        }
        else
        {
            return false;
        }

    }

    public function installment_no_determine()
    {
        $doctor_course_payments = DoctorCoursePayment::where(['doctor_course_id'=>$this->id])->get();
        if(isset($doctor_course_payments) && count($doctor_course_payments))
        {
            $paid_amount = $this->paid_amount();
            $installments = $this->installments();
            if(count($installments))
            {
                $m = 0;
                foreach($installments as $k=>$installment)
                {
                    if($paid_amount >= ($installment->amount*$this->course_price)/100)
                    {
                        $m = $k;
                    }
                    
                }
                return $m;
            }
            
        }
        else
        {
            return 0;
        }

    }

    public function installment_payable_amount()
    {
        //return $this->installment_no_determine();
        // if($this->installment_no_determine() >= count($this->installments()))
        // {
        //     return $payable_amount = 0;
        // }
        // else if($this->installment_no_determine() < count($this->installments()))
        // {
        //     return $payable_amount = $this->installment_gap($this->installment_no_determine()-1,$this->installment_no_determine());
        // }
        $paid_amount = 0;
        $paid_amount = $this->paid_amount();
        $installments = $this->installments();
        if(count($installments))
        {
            foreach($installments as $k=>$installment)
            {
                if($k>=1)
                {
                    if($paid_amount < ($installment->amount*$this->course_price)/100)
                    {
                        //return ($installment->amount*$this->course_price)/100 - ($installments[$k-1]->amount*$this->course_price)/100;
                        return ($installment->amount*$this->course_price)/100 - $paid_amount;
                    }

                }
                else if($k==0)
                {
                    if($paid_amount < ($installment->amount*$this->course_price)/100)
                    {
                        return ($installment->amount*$this->course_price)/100 - $paid_amount;
                    }
                }
                
                
            }
            return 0;
        }

    }

    public function next_installment_last_date()
    {
        $paid_amount = $this->paid_amount();
        $installments = $this->installments();
        if(count($installments))
        {
            foreach($installments as $k=>$installment)
            {
                if($k>=1)
                {
                    if($paid_amount < ($installment->amount*$this->course_price)/100)
                    {
                        return $installment->payment_date;
                    }

                }
                else if($k==0)
                {
                    if($paid_amount < ($installment->amount*$this->course_price)/100)
                    {
                        return $installment->payment_date;
                    }
                }                
                
            }
            return false;
        }

    }

    public function all_installments()
    {
        if($this->batch->payment_times > 1)
        {
            if($this->payment_option == "custom")
            {
                return DoctorCoursePaymentOptions::where(['doctor_course_id'=>$this->id])->get();
            }
            else if($this->payment_option == "default")
            {
                return BatchPaymentOptions::where(['batch_id'=>$this->batch->id])->get();
            }
            else if($this->payment_option == "single" || $this->payment_option == '')
            {
                return DoctorCoursePaymentOptions::where(['doctor_course_id'=>'0'])->get();
            }

        }
        else
        {
            return DoctorCoursePaymentOptions::where(['doctor_course_id'=>'0'])->first();
        }               
    }

    public function next_installment()
    {
        $paid_amount = $this->paid_amount();
        $installments = $this->installments();
        if(isset($installments) && count($installments))
        {
            foreach($installments as $k=>$installment)
            {
                if($k>=1)
                {
                    if($paid_amount < ($installment->amount*$this->course_price)/100)
                    {
                        return $installment;
                    }

                }
                else if($k==0)
                {
                    if($paid_amount < ($installment->amount*$this->course_price)/100)
                    {
                        return $installment;
                    }
                }                
                
            }
            return false;
        }

        return false;

    }

    public function next_payment_eligible()
    {
        $paid_amount = 0;
        $payable_amount = 0;
        $paid_amount = $this->paid_amount();
        
        if($this->batch->payment_times > 1)
        {            
            
            if($this->payment_option == "custom" || $this->payment_option == "default" )
            {
                $installments = $this->installments();
                if(count($installments))
                {
                    foreach($installments as $k=>$installment)
                    {
                        if($k>=1)
                        {
                            if($paid_amount < ($installment->amount*$this->course_price)/100)
                            {
                                $payable_amount = $installment->amount;
                                break;
                            }

                        }
                        else if($k==0)
                        {
                            if($paid_amount < ($installment->amount*$this->course_price)/100)
                            {
                                $payable_amount = $installment->amount;
                                break;
                            }
                        }                
                        
                    }
                }

            }
            else if($this->payment_option == "single" || $this->payment_option == '')
            {
                if($paid_amount < $this->course_price)
                {
                    return true;
                }
                else if($paid_amount >= $this->course_price)
                {
                    return false;
                }

            }

            $payable_amount = ($payable_amount*$this->course_price)/100;
            
            if($paid_amount < $payable_amount)
            {
                return true;
            }
            else if($paid_amount >= $payable_amount)
            {
                return false;
            }
             
        }
        
        return false;

    }

    public function next_installment_gap_amount()
    {
        $paid_amount = $this->paid_amount();
        $installments = $this->installments();
        if(count($installments))
        {
            foreach($installments as $k=>$installment)
            {
                if($k>=1)
                {
                    if($paid_amount < ($installment->amount*$this->course_price)/100)
                    {
                        return ($installment->amount*$this->course_price)/100 - $paid_amount;
                    }

                }
                else if($k==0)
                {
                    if($paid_amount < ($installment->amount*$this->course_price)/100)
                    {
                        return ($installment->amount*$this->course_price)/100 - $paid_amount;
                    }
                }                
                
            }
            return false;
        }

    }

    public function payment_href()
    {
        if ($this->payment_option == 'single')
        {
            return url('/payment/'.$this->id);
        }
        else if ($this->payment_option =='default' || $this->payment_option == 'custom')
        {
            return url('/installment-payment/'.$this->id);
        }
    }

    public function slots()
    {
        $module_schedule_basic_slots = ModuleScheduleSlot::with(['slot','program'])->join('module_schedule','module_schedule_slot.module_schedule_id','module_schedule.id')->whereNull('module_schedule.deleted_at')->join('module','module.id','module_schedule.module_id')->where('module.module_type_id','1')->whereNull('module.deleted_at')->where(['module.institute_id'=>$this->institute_id,'module.course_id'=>$this->course_id,'module.year'=>$this->year,'module.session_id'=>$this->session_id])->join('module_content','module_content.module_id','module.id')->where(['module_content.content_type_id'=>'1','module_content.content_id'=>$this->batch_id])->whereNull('module_content.deleted_at')->get();
        
        // $module_schedule_basic_slots = ModuleScheduleSlot::with(['slot','program','module_schedule','module_schedule.module','module_schedule.module.contents'])->whereHas('module_schedule.module', function ($query) {
        //     $query->where(['module_type_id'=>'1','institute_id'=>$this->institute_id,'course_id'=>$this->course_id,'year'=>$this->year,'session_id'=>$this->session_id]);
        //     })->whereHas('module_schedule.module.contents', function ($query) {
        //         $query->where(['content_type_id'=>'1','content_id'=>$this->batch_id]);
        //     })->get();

        //dd($module_schedule_basic_slots);
        
        if(!isset($module_schedule_basic_slots))
        {
            $module_schedule_basic_slots = Collection::make([]);
        }

        if($this->institute->type == 1)
        {
            if($this->institute_id == 16)
            {   $array_custom_module_ids = array();
                $modules = Module::select('module.*')->where('module.module_type_id','2')->whereNull('module.deleted_at')->where(['module.institute_id'=>$this->institute_id,'module.course_id'=>$this->course_id,'module.year'=>$this->year,'module.session_id'=>$this->session_id])->join('module_content','module_content.module_id','module.id')->where(['module_content.content_type_id'=>'1','module_content.content_id'=>$this->batch_id])->whereNull('module_content.deleted_at')->get();
                if(isset($modules) && count($modules))
                {
                    foreach($modules as $module)
                    {   
                        $module_faculty = ModuleContent::where(['module_id'=>$module->id,'content_type_id'=>'2','content_id'=>$this->faculty_id])->first();
                        $module_discipline = ModuleContent::where(['module_id'=>$module->id,'content_type_id'=>'3','content_id'=>$this->bcps_subject_id])->first();
                        if(isset($module_faculty) && isset($module_discipline))
                        {
                            $array_custom_module_ids[] = $module->id;
                        }
                    }
                }
                $module_schedule_combined_clinical_slots = ModuleScheduleSlot::with(['slot','program'])->join('module_schedule','module_schedule_slot.module_schedule_id','module_schedule.id')->whereNull('module_schedule.deleted_at')->join('module','module.id','module_schedule.module_id')->whereIn('module.id',$array_custom_module_ids)->get();
                //if($this->id == 64186){echo "<pre>";print_r($module_schedule_combined_clinical_slots);exit;}
                return $module_schedule_basic_slots->concat($module_schedule_combined_clinical_slots);
            }
            else
            {
                $module_schedule_bsmmu_clinical_slots = ModuleScheduleSlot::with(['slot','program'])->join('module_schedule','module_schedule_slot.module_schedule_id','module_schedule.id')->whereNull('module_schedule.deleted_at')->join('module','module.id','module_schedule.module_id')->where('module.module_type_id','2')->whereNull('module.deleted_at')->where(['module.institute_id'=>$this->institute_id,'module.course_id'=>$this->course_id,'module.year'=>$this->year,'module.session_id'=>$this->session_id])->join('module_content','module_content.module_id','module.id')->where(['module_content.content_type_id'=>'1','module_content.content_id'=>$this->batch_id])->where(['module_content.content_type_id'=>'2','module_content.content_id'=>$this->faculty_id])->whereNull('module_content.deleted_at')->get();                
                return $module_schedule_basic_slots->concat($module_schedule_bsmmu_clinical_slots);
            }
        }
        else if($this->institute->type == 0)
        {
            $module_schedule_bcps_clinical_slots = ModuleScheduleSlot::with(['slot','program'])->join('module_schedule','module_schedule_slot.module_schedule_id','module_schedule.id')->whereNull('module_schedule.deleted_at')->join('module','module.id','module_schedule.module_id')->where('module.module_type_id','2')->whereNull('module.deleted_at')->where(['module.institute_id'=>$this->institute_id,'module.course_id'=>$this->course_id,'module.year'=>$this->year,'module.session_id'=>$this->session_id])->join('module_content','module_content.module_id','module.id')->where(['module_content.content_type_id'=>'1','module_content.content_id'=>$this->batch_id])->where(['module_content.content_type_id'=>'2','module_content.content_id'=>$this->faculty_id])->whereNull('module_content.deleted_at')->get();
            
            return $module_schedule_basic_slots->concat($module_schedule_bcps_clinical_slots);
        }    
        
        
    }

    public function max_slots()
    {
        $array_slot_list = array();
        $k='';
        foreach($this->slots() as $module_schedule_slot_list)
        {
            $array_k = explode('-',$module_schedule_slot_list->slot->start_time);
            $k = implode('-',array_slice($array_k,0,3));
            $array_slot_list[$k][str_replace('-','',$module_schedule_slot_list->slot->start_time)] = $module_schedule_slot_list;
        }

        ksort($array_slot_list);

        $array_slot_list_custom = array();
        $max = 0;
        foreach($array_slot_list as $k=>$slot_list)
        {
            $count = count($slot_list);
            $max = $max >= $count?$max:$count;
            ksort($slot_list);
            foreach($slot_list as $l=>$slot)
            {
                $array_slot_list_custom[$k][$l] = $slot;
            }
            
        }

        return $max;
    }

    public function array_custom_slot_list()
    {
        $array_slot_list = array();
        $k='';
        foreach($this->slots() as $module_schedule_slot_list)
        {
            $array_k = explode('-',$module_schedule_slot_list->slot->start_time);
            $k = implode('-',array_slice($array_k,0,3));
            $array_slot_list[$k][str_replace('-','',$module_schedule_slot_list->slot->start_time)] = $module_schedule_slot_list;
        }

        ksort($array_slot_list);

        $array_slot_list_custom = array();
        $max = 0;
        foreach($array_slot_list as $k=>$slot_list)
        {
            ksort($slot_list);
            foreach($slot_list as $l=>$slot)
            {
                $array_slot_list_custom[$k][$l] = $slot;
            }
            
        }

        return $array_slot_list_custom;
    }

    public function completed_exam($exam)
    {
        $doctor_exam = DoctorExam::where(['doctor_course_id'=>$this->id,'exam_id'=>$exam->id])->first();
        if(isset($doctor_exam) && $doctor_exam->status == "Completed")
        {
            return true;
        }
        return false;
    }

    public function doctor_asks()
    {
        return $this->hasMany(DoctorAsk::class, 'doctor_course_id', 'id');
    }

    public function request_lecture_videos()
    {
        return $this->hasMany(RequestLectureVideo::class, 'doctor_course_id');
    }

}
