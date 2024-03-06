<?php

namespace App\Http\Controllers;

use App\BatchesSchedules;
use App\Doctors;
use App\DoctorsCourses;
use App\LectureVideo;
use App\PendingLecture;
use App\RequestLectureVideo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestLectureVideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:doctor');
    }

    public function index()
    {
        // return
        $doctor = Doctors::findOrFail(Auth::guard('doctor')->id());

        $doctor_courses = DoctorsCourses::query()
            ->select('id', 'doctor_id', 'batch_id', 'course_id')
            ->with([
                'batch:id,name,year,branch_id',
                'course:id,name',
            ])
            ->where([
                'doctor_id' => $doctor->id,
                'status' => 1,
                'is_trash' => 0,
                'payment_status' => "Completed",
            ])
            ->whereHas('batch', function ($query) {
                $query
                    ->where('branch_id', '!=', 4)
                    ->where([
                        'status' => 1,
                        // 'enable_request_for_lecture' => 1, // For dynamic enable from admin
                    ]);
            })
            ->get();

        // return [
        //     'total' => $doctor_courses->count(),
        //     'doctor_courses' => $doctor_courses
        // ];

        return view('request_lecture_video.index', compact('doctor_courses'));
    }

    public function show(DoctorsCourses $doctor_course, $id = null)
    {
        $allow_request_number = 5;

        // return
        $doctor_course->load([
            'course:id,name',
            'batch:id,name',
        ]);

        // return
        $current_watching_video = RequestLectureVideo::query()
            ->with('pending_video')
            ->whereHas('pending_video')
            ->where([
                "status" => 1,
                "doctor_course_id" => $doctor_course->id,
            ])
            ->first();

        $has_current_watching_video = (boolean) ($current_watching_video && $current_watching_video->count());

        // return [
        //     $has_current_watching_video,
        // ];

        $current_lecture_video = '';
        
        if($has_current_watching_video) {
            if($current_watching_video->end < Carbon::now()) {
                $current_watching_video->update([
                    "status" => 2
                ]);
                $has_current_watching_video = false;
                $current_lecture_video = "";
            } else {
                $current_lecture_video = $current_watching_video->pending_video ?? "";
            }

        } 
        else if($id) {
            // return
            $request_lecture_video = RequestLectureVideo::query()
                ->with('pending_video')
                ->where([
                    'id' => $id,
                    'doctor_course_id' => $doctor_course->id
                ])
                ->whereNull('end')
                ->first();

            if ($request_lecture_video) {
                $request_lecture_video->update([
                    "status"    => 1,
                    "end"       => date('Y-m-d G:i:s', strtotime("+7 day")),
                ]);
                $has_current_watching_video = true;
                $current_lecture_video = $request_lecture_video->pending_video ?? "";
            }
        }

        // return
        $request_lecture_videos = RequestLectureVideo::query()
            ->with('pending_video')
            ->has('pending_video')
            ->where([
                'doctor_course_id' => $doctor_course->id,
            ])
            ->take($allow_request_number)
            ->get();

        $request_available = (int) ($allow_request_number - $request_lecture_videos->count());

        // return [
        //     $request_available,
        // ];

        // return $request_lecture_videos->pluck('pending_video_id');

        // return
        $batch_pending_videos = $doctor_course
            ->batch()
            ->first()
            ->pending_videos()
            ->with('video:id,name')
            ->whereNotIn('id', $request_lecture_videos->pluck('pending_video_id')) // remove previous 
            ->get();

        // return [
        //      $has_current_watching_video,
        // ];

        // return $current_lecture_video;

        return view('request_lecture_video.show', compact(
            'doctor_course',
            'current_lecture_video',
            'request_available',
            'request_lecture_videos',
            'batch_pending_videos',
            'has_current_watching_video'
        ));
    }

    public function request(Request $request, DoctorsCourses $doctor_course)
    {
        if($doctor_course->doctor->id == Auth::guard('doctor')->id()) {
            $doctor_course->request_lecture_videos()->create([
                'pending_video_id' => $request->pending_video_id,
            ]);
        }

        return back();
    }

    public function complete(DoctorsCourses $doctor_course, $pending_video_id)
    {
        // return $pending_video_id;
        if($doctor_course->doctor->id == Auth::guard('doctor')->id()) {
            RequestLectureVideo::query()
                ->where([
                    'doctor_course_id'      => $doctor_course->id,
                    'pending_video_id'    => $pending_video_id,
                ])
                ->update([
                    'status' => 2
                ]);
        }

        return back();
    }
}
