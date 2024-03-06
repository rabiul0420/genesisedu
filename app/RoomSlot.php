<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomSlot extends Model
{
    protected $table = 'room_slot';

    use SoftDeletes, ScheduleDefs;

    public function room()
    {
        return $this->belongsTo('App\Room','room_id','id');
    }

    public function date()
    {
        $array_start_time = explode('-',$this->start_time);
        return $start_time = implode('-',array_slice($array_start_time,0,3));
    }

    public function custom_date()
    {
        $array_start_time = explode('-',$this->start_time);
        $array_sliced = array_slice($array_start_time,0,3);
        $year = $array_sliced[0];
        $month = $array_sliced[1];
        $day = $array_sliced[2];
        return $custom_date = implode('-',array($day,$month,$year));
    }

    public function custom_start_time()
    {
        $ampm = '';
        $array_start_time = explode('-',$this->start_time);
        $time = array_slice($array_start_time,-2,2);
        if($time[0]<12)$ampm = 'AM';
        else $ampm = "PM";

        if($time[0]%12 == 0 && $time[0] == 12)
        {
            $first_part = 12;
        }
        else
        {
            $first_part = str_pad(($time[0]%12),2,'0',STR_PAD_LEFT);
        }
        
        return $start_time = $first_part.':'.$time[1].' '.$ampm;
    }

    public function custom_end_time()
    {
        $ampm = '';
        $array_end_time = explode('-',$this->end_time);
        $time = array_slice($array_end_time,-2,2);
        if($time[0]<12)$ampm = 'AM';
        else $ampm = "PM";

        if($time[0]%12 == 0 && $time[0] == 12)
        {
            $first_part = 12;
        }
        else
        {
            $first_part = str_pad(($time[0]%12),2,'0',STR_PAD_LEFT);
        }

        return $end_time = $first_part.':'.$time[1].' '.$ampm;
    }

    public function hrstart_time()
    {
        $ampm = '';
        $array_start_time = explode('-',$this->start_time);
        $time = array_slice($array_start_time,-2,2);
        if($time[0]<12)$ampm = 'AM';
        else $ampm = "PM";

        if($time[0]%12 == 0 && $time[0] == 12)
        {
            $first_part = 12;
        }
        else
        {
            $first_part = str_pad(($time[0]%12),2,'0',STR_PAD_LEFT);
        }
        
        return $start_time = $first_part.' : '.$time[1].' '.$ampm;
    }

    public function hrend_time()
    {
        $ampm = '';
        $array_end_time = explode('-',$this->end_time);
        $time = array_slice($array_end_time,-2,2);
        if($time[0]<12)$ampm = 'AM';
        else $ampm = "PM";

        if($time[0]%12 == 0 && $time[0] == 12)
        {
            $first_part = 12;
        }
        else
        {
            $first_part = str_pad(($time[0]%12),2,'0',STR_PAD_LEFT);
        }

        return $end_time = $first_part.' : '.$time[1].' '.$ampm;
    }

    public function start_time_end_time()
    {
        return $this->hrstart_time().' - '.$this->hrend_time();
    }

}
