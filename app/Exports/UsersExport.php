<?php
namespace App\Exports;

use App\DoctorsCourses;
use App\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromQuery, WithHeadings
{
    use Exportable;

    public function __construct(int $params)
    {
        $this->params = $params;
    }

    public function query()
    {

        $params_array = explode('_',session('paras'));

        $barches = DoctorsCourses::query()->select('doctors_courses.id','d2.name as doctor_name', DB::raw("CONCAT('88',mobile_number) AS mobile_number"), 'd2.bmdc_no as bmdc_no','doctors_courses.reg_no','d3.name as institute_name','d4.name as course_name','d5.name as faculty_name','d6.name as subject_name','d7.name as batche_name','doctors_courses.year','d8.name as session_name','d10.name as branch_name',  'doctors_courses.created_at', 'doctors_courses.payment_status')
            ->leftjoin('doctors as d2', 'doctors_courses.doctor_id', '=','d2.id')
            ->leftjoin('institutes as d3', 'doctors_courses.institute_id', '=','d3.id')
            ->leftjoin('courses as d4', 'doctors_courses.course_id', '=','d4.id')
            ->leftjoin('faculties as d5', 'doctors_courses.faculty_id', '=','d5.id')
            ->leftjoin('subjects as d6', 'doctors_courses.subject_id', '=','d6.id')
            ->leftjoin('batches as d7', 'doctors_courses.batch_id', '=','d7.id')
            ->leftjoin('sessions as d8', 'doctors_courses.session_id', '=','d8.id')
            ->leftjoin('service_packages as d9', 'doctors_courses.service_package_id', '=','d9.id')
            ->leftjoin('branches as d10', 'doctors_courses.branch_id', '=','d10.id')
            ->where(['doctors_courses.year'=>$params_array[0],'doctors_courses.session_id'=>$params_array[1],'batch_id'=>$params_array[2],'doctors_courses.subject_id'=>$params_array[3],'is_trash'=>0]);

        return $barches;
        
    }

    public function headings(): array
    {
        return [
            'ID',
            'Doctor Name',
            'Phone',
            'BMDC No',
            'Registration No',
            'Institute',
            'Course',
            'Faculty',
            'Discipline',
            'Batch',
            'Year',
            'Session',
            'Branch',
            'Admission Time',
            'Payment Status',
        ];
    }
}
