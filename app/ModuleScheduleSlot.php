<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleScheduleSlot extends Model
{
    protected $table = 'module_schedule_slot';

    use SoftDeletes, ScheduleDefs;

    public function module_schedule()
    {
        return $this->belongsTo('App\ModuleSchedule','module_schedule_id','id');
    }

    public function slot()
    {
        return $this->belongsTo('App\RoomSlot','slot_id','id');
    }

    public function program()
    {
        return $this->belongsTo('App\Program','program_id','id');
    }

    public function add_link()
    {
        return $edit_link = url("admin/module-schedule-slot-add/".$this->module_schedule->id );
    }

    public function edit_link()
    {
        return $edit_link = url("admin/module-schedule-slot-edit/".$this->id );
    }

    public function delete_link()
    {
        return $delete_link = url("admin/module-schedule-slot-delete/".$this->id );
    }

    public function program_add_link()
    {
        return $edit_link = url("admin/module-schedule-program-add/".$this->id );
    }

    public function program_edit_link()
    {
        return $edit_link = url("admin/module-schedule-program-edit/".$this->id );
    }

    public function program_delete_link()
    {
        return $edit_link = url("admin/module-schedule-program-delete/".$this->id );
    }

    public function room_name()
    {
        return $room_name = $this->slot->room->name;
    }

    public function time_span()
    {
        return $slot_duration_string = $this->slot->hrstart_time().' - '.$this->slot->hrend_time();
    }

}
