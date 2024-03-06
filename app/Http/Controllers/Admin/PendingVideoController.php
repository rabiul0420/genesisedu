<?php

namespace App\Http\Controllers\Admin;

use App\Batches;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Institutes;
use App\LectureVideo;
use App\PendingVideo;

class PendingVideoController extends Controller
{
    public function index()
    {
        $batches = Batches::query()
            ->with([
                'course:id,name',
                'session:id,name',
                'faculties',
                'subjects',
            ])
            ->offline()
            ->active()
            ->latest()
            ->select([
                'id',
                'name',
                'year',
                'course_id',
                'session_id',
                'batch_type',
            ])
            ->paginate(20);

        return view('tailwind.admin.pending-videos.index', compact('batches'));
    }

    public function show(Batches $batch)
    {
        // return
        $batch->load('pending_videos');

        return view('tailwind.admin.pending-videos.show', compact('batch'));
    }

    public function prepare(Batches $batch)
    {
        $selected_content_ids = Array();
        $contents = null;
        $search = request()->search;
        $page = request()->page;

        if(!request()->flag) {
            return view('tailwind.admin.pending-videos.prepare', compact(
                'batch',
                'search',
                'page',
            ));
        }

        $contents = $this->getContent(LectureVideo::query(), $batch)
            ->appends(['search' => request()->search]);

        $selected_content_ids = $this->getSelectedContentId($batch);

        return response([
            "html"          => view('tailwind.admin.pending-videos.data', compact('contents', 'selected_content_ids'))->render(),
            "message"       => "Success",
            "totalContent"  => count($selected_content_ids),
        ], 200);
    }

    protected function getContent($query, $batch)
    {
        return $query
            // ->orderBy('name')
            ->with('class')
            ->when(request()->search, function ($query, $search) {
                $query->where(function($query) use ($search) {
                    $query->where('id', $search)
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->whereHas('class', function ($query) use ($batch) {
                $query->where('year', $batch->year)
                    ->where('course_id', $batch->course_id)
                    ->where('session_id', $batch->session_id);
            })
            ->latest('id')
            ->paginate(16);
    }

    protected function getSelectedContentId($batch)
    {
        return $batch
            ->pending_videos()
            ->pluck('video_id')
            ->toArray();
    }

    public function assign(Request $request, Batches $batch)
    {
        $video = LectureVideo::where('id', $request->video_id)->first() ?? null;

        if($video) {
            $pending_video = PendingVideo::withTrashed()->updateOrCreate(
                [
                    'batch_id' => $batch->id,
                    'video_id' => $video->id,
                ],
                [
                    'deleted_at' => $request->checked ? NULL : now(),
                    'priority' => $request->checked ? $this->getSuitablePriority($batch) : 0,
                ]
            );
        }

        return response([
            'video_id'      => $request->video_id,
            'message'       => $request->checked ? 'Added!' : 'Removed!',
            'totalContent'  => $batch->pending_videos()->count(),
        ], 200);
    }

    protected function getSuitablePriority($batch, $priority = null)
    {
        if($priority) {
            return $priority;
        }

        $max_priority = PendingVideo::where('batch_id', $batch->id)->max('priority');

        return $max_priority + 1;
    }
}
