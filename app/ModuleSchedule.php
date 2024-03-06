<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleSchedule extends Model
{
    protected $table = 'module_schedule';

    use SoftDeletes, ScheduleDefs;

    public function module()
    {
        return $this->belongsTo('App\Module','module_id','id');
    }

    public function slots()
    {
        return $this->hasMany('App\ModuleScheduleSlot','module_schedule_id','id');
    }

    public function max_slots()
    {
        $array_slot_list = array();
        $k='';
        foreach($this->slots as $module_schedule_slot_list)
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
        foreach($this->slots as $module_schedule_slot_list)
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

}
