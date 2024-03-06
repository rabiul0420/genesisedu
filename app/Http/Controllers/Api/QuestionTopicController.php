<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\QuestionTopic;

class QuestionTopicController extends Controller
{
    public function index()
    {
        $request = request();

        $question_topics = QuestionTopic::query()
            ->with('chapter.subject')
            ->has('chapter.subject')
            ->when($request->chapter, function($query, $chapter_id) {
                $query->where('chapter_id', $chapter_id);
            })
            ->when($request->subject, function($query, $subject_id) {
                $query->whereHas('chapter', function($query) use ($subject_id) {
                    $query->where('subject_id', $subject_id);
                });
            })
            ->select([
                'id',
                'topic_name as name',
                'chapter_id',
                'subject_id',
            ])
            ->orderBy('name')
            ->get();

        $data = Array();

        $question_topics->map(function($chapter) use (&$data) {
            $data[] = [
                'id'        => (int) $chapter->id,
                'name'      => (string) ($chapter->name ?? ''),
                'chapterId' => (int) ($chapter->subject_id),
                'subjectId' => (int) ($chapter->subject_id),
            ];
        });

        return response($data, 200);
    }
}
