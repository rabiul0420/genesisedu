<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Sessions;

class SessionController extends Controller
{
    public function index()
    {
        $request = request();

        $sessions = Sessions::query()
            ->whereHas('course_years', function ($query) use ($request) {
                $query
                    ->where('status', 1)
                    ->when($request->course_id, function ($query, $course_id) {
                        $query->where('course_id', $course_id);
                    })
                    ->when($request->year, function ($query, $year) {
                        $query->where('year', $year);
                    });
            })
            ->get([
                'id',
                'name',
                // 'show_admission_form',
            ]);

        return $sessions;
    }
}
