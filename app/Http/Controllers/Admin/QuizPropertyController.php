<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Institutes;
use App\Question;
use App\QuizProperty;
use App\QuizPropertyItem;

class QuizPropertyController extends Controller
{
    private $total_question = 0;
    private $full_mark = 0.0;
    private $title = "";

    public function index()
    {
        $quiz_properties = QuizProperty::query()
            ->with([
                'quiz_property_items',
                'course:id,name',
                'quizzes:id,quiz_property_id',
            ])
            ->latest()
            ->paginate();

        $institutes = Institutes::query()
                ->active()
                ->with('active_courses:id,name,institute_id')
                ->get([
                    'id',
                    'name'
                ]);

        return view('tailwind.admin.quiz-properties.index', compact('quiz_properties', 'institutes'));
    }

    public function save(Request $request, $quiz_property_id = null)
    {
        $this->setRequestValue($request);

        $quiz_property = QuizProperty::updateOrCreate(
            [
                'id'                => $quiz_property_id,
            ],
            [
                'course_id'         => $request->course_id,
                'duration'          => $request->duration,
                'pass_mark_percent' => $request->pass_mark_percent,
                'status'            => $request->status ?? 1,
                'total_question'    => $this->total_question,
                'full_mark'         => $this->full_mark,
                'title'             => $this->title,
                'deleted_at'        => NULL,
            ]
        );

        $quiz_property->quiz_property_items()->delete();

        if(is_array($request->question_type)) {
            foreach($request->question_type as $index => $question_type) {
                if($request->number_of_question[$index]) {
                    QuizPropertyItem::onlyTrashed()->updateOrCreate(
                        [
                            'quiz_property_id'              => $quiz_property->id,
                        ],
                        [
                            'question_type'             => $question_type,
                            'number_of_question'        => $request->number_of_question[$index],
                            'per_stamp_mark'            => $request->per_stamp_mark[$index],
                            'per_stamp_negative_mark'   => $request->per_stamp_negative_mark[$index],
                            'deleted_at'                => NULL,
                        ]
                    );
                }
            }
        }

        if($quiz_property_id) {
            return back();
        }

        return redirect()->route('quiz-properties.index');
    }

    protected function setRequestValue($request)
    {
        $total_question = 0;
        $full_mark = 0.0;
        $title = "";

        if(is_array($request->question_type)) {
            foreach($request->question_type as $index => $question_type) {
                if($request->number_of_question[$index]) {
                    $total_question += $request->number_of_question[$index];
                    $full_mark      += ($request->number_of_question[$index] * $request->per_stamp_mark[$index] * Question::$stamp_of_question_array[$question_type]);
                    $title          .= ($request->number_of_question[$index] . " " . Question::$question_type_array[$question_type] . ' + ');
                }
            }
        }

        $this->total_question = $total_question;
        $this->full_mark = $full_mark;
        $this->title = trim($title, ' + ');
    }
}
