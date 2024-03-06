<?php

namespace App;
use Auth;
use Carbon\Carbon;

trait SendSms
{
    public function send_sms($doctor,$sms)
    {
        $postvars = array(
            'userID'=>"Genesis",
            'passwd'=>"genesisAPI@019",
            'sender'=>"8801969901099",  
            'msisdn'=> '88' .$doctor->mobile_number,
            'message'=>htmlspecialchars($sms->sms),
        );
        
 
        $string = "https://vas.banglalink.net/sendSMS/sendSMS/";

        $ch = curl_init();
        $url = "https://vas.banglalink.net/sendSMS/sendSMS";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);
        print "curl response is:" . //$response;echo "<pre>";print_r($response);exit;
        curl_close ($ch);

        $admin_id = Auth::user()->id;        
        $event_type = $sms->sms_event->id ?? 0;
        $event = $sms->sms_event->name ?? '';
        $delivery_status = "success";
        if(strpos($response,"Success Count : 1") !== null)
        {
            $delivery_status = "success";
        }
        else
        {
            $delivery_status = "failed";
        };

        $sms_log = SmsLog::insert([
            'doctor_id'=>$doctor->id,
            'mobile_no'=>$doctor->mobile_number,
            'event_type'=>$event_type,
            'event'=>$event,
            'delivery_status' => $delivery_status,
            'admin_id' => $admin_id,
            'created_at' => Carbon::now(),
        ]);
        return $sms_log;

    }

    public function send_custom_sms($doctor, $smsText, $event, $isAdmin = true)
    {
        $postvars = array(
            'userID'    => "Genesis",
            'passwd'    => "genesisAPI@019",
            'sender'    => "8801969901099",
            'msisdn'    => '88' . $doctor->mobile_number,
            'message'   => $smsText,
        );
        
        $string = "https://vas.banglalink.net/sendSMS/sendSMS/";

        $ch = curl_init();
        $url = "https://vas.banglalink.net/sendSMS/sendSMS";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);
        print "curl response is:" . //$response;echo "<pre>";print_r($response);exit;
        curl_close ($ch);

        $admin_id = ($isAdmin == true) ? Auth::id() : '';
        $sms_event = SmsEvent::where(['name'=>$event])->first();
        $event_type = $sms_event->id ?? 0;
        $event = $sms_event->name ?? '';
        $delivery_status = "success";
        if(strpos($response,"Success Count : 1") !== null)
        {
            $delivery_status = "success";
        }
        else
        {
            $delivery_status = "failed";
        };

        $sms_log = SmsLog::insert([
            'doctor_id'=>$doctor->id,
            'mobile_no'=>$doctor->mobile_number,
            'event_type'=>$event_type,
            'event'=>$event,
            'delivery_status' => $delivery_status,
            'admin_id' => ($isAdmin == true) ? $admin_id : '',
            'created_at' => Carbon::now(),

        ]);
        return $sms_log;

    }

    
    public function send_custom_sms_unregistered($mobile_number, $smsText, $event, $isAdmin = true)
    {
        $postvars = array(
            'userID'    => "Genesis",
            'passwd'    => "genesisAPI@019",
            'sender'    => "8801969901099",
            'msisdn'    => '88' . $mobile_number,
            'message'   => $smsText,
        );
        
        $string = "https://vas.banglalink.net/sendSMS/sendSMS/";

        $ch = curl_init();
        $url = "https://vas.banglalink.net/sendSMS/sendSMS";
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST, 1);                //0 for a get request
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
        curl_setopt($ch,CURLOPT_TIMEOUT, 20);
        $response = curl_exec($ch);
        print "curl response is:" . //$response;echo "<pre>";print_r($response);exit;
        curl_close ($ch);

        $admin_id = ($isAdmin == true) ? Auth::id() : '';
        $sms_event = SmsEvent::where(['name'=>$event])->first();
        $event_type = $sms_event->id ?? 0;
        $event = $sms_event->name ?? '';
        $delivery_status = "success";
        if(strpos($response,"Success Count : 1") !== null)
        {
            $delivery_status = "success";
        }
        else
        {
            $delivery_status = "failed";
        };

        $sms_log = SmsLog::insert([
            // 'doctor_id'=>$doctor->id,
            'mobile_no'=>$mobile_number,
            'event_type'=>$event_type,
            'event'=>$event,
            'delivery_status' => $delivery_status,
            'admin_id' => ($isAdmin == true) ? $admin_id : '',
            'created_at' => Carbon::now(),
        ]);
        return $sms_log;
        
    }


    public function schedule_module_content_types()
    {
        return $moduleContentTypes = ScheduleModuleContentType::get();
    }
    
    public function schedule_media_types()
    {
        return $mediaTypes = ScheduleMediaType::get();
    }

    public function schedule_batch_types()
    {
        return $batchTypes = ScheduleBatchType::get();
    }

    public function program_types()
    {
        return $programTypes = ScheduleProgramType::get();;
    }

    public function schedule_program_content_types()
    {
        return $programContentTypes = ScheduleProgramContentType::get();
    }

    public function schedule_topic_program_content_type($topic_content_type_id)
    {
        return $programContentTypes = ScheduleProgramContentType::where('topic_content_type_id',$topic_content_type_id)->first();
    }
    
}
