<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PendingLecture;

class RequestLectureVideoController extends Controller
{
    public function index()
    {
        // return
        $pending_lectures = PendingLecture::query()
            ->with('request_lecture_videos')
            ->orderBy('link')
            ->paginate();

        return view('tailwind.admin.request-lecture-videos.index', compact('pending_lectures'));
    }

    public function update(Request $request, PendingLecture $pending_lecture)
    {
        $response = $pending_lecture->update([
            "link" => $request->link,
            "password" => $request->password,
        ]);

        return response([
            'status' => (boolean) $response,
            'message' => (string) $response ? "Success" : "Try Again",
            'hasLink' => (boolean) $pending_lecture->link
        ], 200);
    }
}
