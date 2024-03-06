<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\QuestionChapter;

class QuestionChapterController extends Controller
{
    public function index()
    {
        $request = request();

        $question_chapters = QuestionChapter::query()
            ->with('subject')
            ->has('subject')
            ->when($request->subject, function($query, $subject_id) {
                $query->where('subject_id', $subject_id);
            })
            ->select([
                'id',
                'chapter_name as name',
                'subject_id',
            ])
            ->orderBy('name')
            ->get();

        $data = Array();

        $question_chapters->map(function($chapter) use (&$data) {
            $data[] = [
                'id'        => (int) $chapter->id,
                'name'      => (string) ($chapter->name ?? ''),
                'subjectId' => (int) ($chapter->subject_id),
            ];
        });

        return response($data, 200);
    }
}
