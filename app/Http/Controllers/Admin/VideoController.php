<?php

namespace App\Http\Controllers\Admin;

use App\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class VideoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.videos.index', [
            'videos' => Video::get()
        ]);
    }

    public function create()
    {
        return view('admin.videos.create', [
            'video' => new Video()
        ]);
    }

    public function store(Request $request)
    {
        $video = Video::create($this->validation($request));

        return redirect()
            ->route('videos.show', $video->id)
            ->with('message', 'The record has been successfully added.');
    }

    public function show(Video $video)
    {
        return view('admin.videos.show', compact('video'));
    }

    public function edit(Video $video)
    {
        return view('admin.videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $video->update($this->validation($request, $video->id));

        return redirect()
            ->route('videos.show', $video->id)
            ->with('message', 'The record has been successfully updated.');
    }

    public function destroy(Video $video)
    {
        $video->delete();

        return redirect()
            ->route('videos.index')
            ->with('message', 'The record has been successfully deleted.');
    }

    private function validation($request, $id = '')
    {
        return $request->validate([
            'name' => [
                'required',
                'max:250',
                Rule::unique('videos', 'name')->ignore($id)
            ],
            'url' => [
                'required',
                'max:250',
                Rule::unique('videos', 'url')->ignore($id)
            ],
        ]);
    }
}
