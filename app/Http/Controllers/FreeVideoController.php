<?php

namespace App\Http\Controllers;

use App\Video;
use Illuminate\Http\Request;

class FreeVideoController extends Controller
{
    public function show(Video $video)
    {
        return view('pages.videos.show', compact('video'));
    }
}
