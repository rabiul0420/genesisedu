<?php

namespace App\Http\Controllers;

use App\MedicalCollege;
use App\Subjects;
use App\SuccessfullFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SuccessController extends Controller
{
    public function add_personal_details(){
//         Session::forget('bmdc_no');
// return Session::get('bmdc_no');
       $medical_college = MedicalCollege::pluck('name','id');
      $subjects= Subjects::pluck('name','id');
        return view('success_list.add_personal_details',compact('medical_college','subjects'));
    }
    public function successfull_personal_detail_submit(Request $request){
       $personal_detail = new SuccessfullFeedback;
       $personal_detail->name = $request->name;
       $personal_detail->bmdc_no = $request->bmdc_no;
       $personal_detail->medical_college_id = $request->medical_college_id;
       $personal_detail->mobile_number = $request->mobile_number;
       $personal_detail->discipline_id = $request->discipline_id;
       $personal_detail->address = $request->address;
       $personal_detail->save();
       Session::put('id',$personal_detail->id);
    //    Session::put('mobile_number',$personal_detail->mobile_number);

       return redirect('genesis-batch-details');
    }

    public function genesis_batch_details(){
        return view('success_list.genesis_batch_details');
    }

    public function genesis_batch_details_submit(Request $request){
       $id = Session::get('id');
    //    $bmdc_no_a = 'A'.$bmdc_no;
        // $mobile_number = Session::get('mobile_number');

       $feedback = SuccessfullFeedback::find($id);
       $feedback->batch_name = $request->batch_name;
       $feedback->year = $request->year;
       $feedback->session = $request->session;
        $feedback->push();
        return redirect('feedback-about-genesis');

    }

    public function feedback_about_genesis(){
        return view('success_list.feedback_about_genesis');

    }

    public function feedback_about_submit(Request $request){
            $id = Session::get('id');

            $feedback = SuccessfullFeedback::find($id);

           $feedback->regular_class = $request->regular_class;
           $feedback->zoom_live_class = $request->zoom_live_class;
           $feedback->exam_class = $request->exam_class;
           $feedback->solve_class = $request->solve_class;
           $feedback->lecture_sheet = $request->lecture_sheet;
           $feedback->it_support = $request->it_support;
           $feedback->push();
           return redirect('struggling-history');
    }

    public function struggling_history(){
        return view('success_list.struggling_history');
    }

    public function struggling_history_submit(Request $request){
        $id = Session::get('id');

            $feedback = SuccessfullFeedback::find($id);
        $feedback->struggling_history = $request->struggling_history;
        $feedback->push();
        return redirect('effective-service');
    }

    public function effective_service(){
        return view('success_list.effective_service');
    }

    public function effective_service_submit(Request $request){
        $id = Session::get('id');

            $feedback = SuccessfullFeedback::find($id);
        $feedback->effective_service = $request->effective_service;
        $feedback->service_improve = $request->service_improve;
        $feedback->overall_value = $request->overall_value;
        $feedback->push();
        if($request->image){
            $image = $request->file('image');

            $name =  time().'-'.$image->getClientOriginalName();
            $uploadpath = 'uploads/success_image/';
            $image->move($uploadpath, $name);
            $imageUrl = $uploadpath.$name;
            $feedback->image = $imageUrl;
            $feedback->push();
        }
        Session::forget('id');
        Session::flash('class', 'alert-success');
        session()->flash('message', 'Thank you so much for your feedback . It will help us to improve. Stay Safe');
        return view('success_list.success-information-successfully-add');
    }
}
