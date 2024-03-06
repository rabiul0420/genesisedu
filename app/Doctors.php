<?php

namespace App;


use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctors extends Authenticatable
{

    use SoftDeletes;

    use Notifiable;

    use HasRoles;


    protected $table = 'doctors';

    protected $guarded = [];

    protected $appends = [
        'phone',
        'is_vip',
    ];

    public function getPhoneAttribute()
    {
        return $this->mobile_number;
    }

    public function getIsVipAttribute($value)
    {
        return $this->vip ? 1 : 0;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['mobile_number'] = $value;
    }

    public function scopeSubscriber()
    {
        return $this->where('subscriber', 1);
    }
    
    public function doctorcourses()
    {
        return $this->hasMany(DoctorsCourses::class, 'doctor_id', 'id')->orderBy('doctors_courses.id','desc');
    }
    
    public function doctor_courses()
    {
        return $this->hasMany(DoctorsCourses::class, 'doctor_id', 'id')->orderBy('doctors_courses.id','desc');
    }

    public function active_doctor_courses()
    {
        return $this->hasMany(DoctorsCourses::class, 'doctor_id', 'id')->where('is_trash', 0);
    }

    public function medicalcolleges()
    {
        return $this->belongsTo('App\MedicalColleges','medical_college_id','id');
    }

    public function medical_college()
    {
        return $this->belongsTo(MedicalColleges::class, 'medical_college_id', 'id');
    }

    public function present_thana()
    {
        return $this->belongsTo('App\Upazilas','present_upazila_id','id');
    }

    public function present_upazila()
    {
        return $this->belongsTo('App\Upazilas','present_upazila_id','id');
    }

    public function present_district()
    {
        return $this->belongsTo('App\Districts','present_district_id','id');
    }

    public function present_division()
    {
        return $this->belongsTo('App\Divisions','present_division_id','id');
    }

    public function permanent_thana()
    {
        return $this->belongsTo('App\Upazilas','permanent_upazila_id','id');
    }

    public function permanent_upazila()
    {
        return $this->belongsTo('App\Upazilas','permanent_upazila_id','id');
    }

    public function permanent_district()
    {
        return $this->belongsTo('App\Districts','permanent_district_id','id');
    }

    public function permanent_division()
    {
        return $this->belongsTo('App\Divisions','permanent_division_id','id');
    }
    public function get_status(){
        return $this->status? "Active":"InActive";
    }

    public function discount()
    {
        return $this->hasMany('App\Discount','doctor_id','id');
    }

    public function subscription_orders()
    {
        return $this->hasMany(SubscriptionOrder::class, 'doctor_id', 'id');
    }

    public function batch_shift_sms_logs()
    {
        return $this->hasMany(SmsLog::class, 'doctor_id')
            ->where('sms_logs.event', 'Batch Shifted');
    }
}
