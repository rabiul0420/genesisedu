<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\QuestionSubject;

class QuestionSubjectController extends Controller
{
    public function index()
    {
        $question_subjects = QuestionSubject::query()            
            ->select([
                'id',
                'subject_name as name',
            ])
            ->orderBy('name')
            ->get();

        $data = Array();

        $question_subjects->map(function($subject) use (&$data) {
            $data[] = [
                'id'    => (int) $subject->id,
                'name'  => (string) ($subject->name ?? ''),
            ];
        });

        return response($data, 200);
    }
}
