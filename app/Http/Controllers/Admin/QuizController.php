<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Question;
use App\Quiz;
use App\QuizParticipant;
use App\QuizProperty;
use App\QuizQuestion;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::query()
            ->with([
                'quiz_questions',
                'quiz_property.quiz_property_items',
            ])
            ->latest()
            ->paginate();

        $quiz_properties = QuizProperty::query()
            ->with('quiz_property_items')
            ->get();

        return view('tailwind.admin.quizzes.index', compact('quizzes', 'quiz_properties'));
    }

    public function show(Quiz $quiz)
    {
        $quiz_properties = QuizProperty::query()
            ->with('quiz_property_items')
            ->get();

        $quiz->load([
            'quiz_property.quiz_property_items:id,quiz_property_id,number_of_question,question_type',
            'quiz_participants:id,quiz_id',
        ]);

        $quiz_participants_count = $quiz->quiz_participants->count();

        $number_of_question_by_question_type = Array();
        
        $quiz->quiz_property->quiz_property_items->map(function($item) use (&$number_of_question_by_question_type) {
            $number_of_question_by_question_type[$item->question_type] = $item->number_of_question;
        });

        $quiz_questions = $quiz->quiz_questions()
            ->with('question.question_options')
            ->get();

        $qr_code_image = $this->getQRCode(route('on-spot-quiz.show', $quiz->key), 80);

        return view('tailwind.admin.quizzes.show', compact('quiz', 'quiz_properties', 'quiz_questions', 'qr_code_image', 'number_of_question_by_question_type', 'quiz_participants_count'));
    }

    private function getQRCode($text = "", $size = 50)
    {
        return \QrCode::size($size)
            ->generate($text);
    }

    public function save(Request $request, $quiz_id = null)
    {
        if(QuizParticipant::where('quiz_id', $quiz_id)->exists()) {
            $quiz = Quiz::where('id', $quiz_id)->first();
            
            $quiz->update(['status' => $request->status == 1 ? 1 : 2]);
        } else {
            $quiz = Quiz::updateOrCreate(
                [
                    'id'                => $quiz_id,
                ],
                [
                    'title'             => $request->title,
                    'quiz_property_id'  => $request->property_id,
                    'status'            => $request->status ?? 0,
                ]
            );
        }

        if($request->status == 1) {
            $quiz->load([
                'quiz_property'
            ]);

            if($quiz->quiz_property->total_question != $quiz->quiz_questions->count()) {
                $quiz->update([
                    'status' => 0,
                ]);
            }
        }

        return redirect()->route('quizzes.show', $quiz->id);
    }

    public function prepare(Quiz $quiz, $question_type)
    {
        $selected_question_ids = Array();
        $questions = null;
        $search = request()->search;
        $page = request()->page;
        $question_type_text = Question::getQuestionTypeText($question_type);

        $property_number_of_question = $quiz->quiz_property
            ->quiz_property_items
            ->where('question_type', $question_type)
            ->first()
            ->number_of_question ?? 0;

        if(!request()->flag) {
            return view('tailwind.admin.quizzes.prepare', compact(
                'quiz',
                'question_type',
                'question_type_text',
                'search',
                'page',
                'property_number_of_question',
            ));
        }

        $questions = $this->getQuestion(Question::where('type', $question_type))
            ->appends(['search' => request()->search]);

        $selected_question_ids = $this->getSelectedQuestionId($quiz, $question_type);

        return response([
            "html"          => view('tailwind.admin.quizzes.data', compact('questions', 'selected_question_ids'))->render(),
            "message"       => "Success",
            "totalQuestion" => count($selected_question_ids),
        ], 200);
    }

    protected function getQuestion($query)
    {
        return $query
            // ->orderBy('name')
            ->when(request()->search, function ($query, $search) {
                $query->where(function($query) use ($search) {
                    $query->where('id', $search)
                        ->orWhere('question_title', 'like', "%{$search}%");
                });
            })
            ->when(request()->subject, function ($query, $subject_id) {
                $query->where('subject_id', $subject_id);
            })
            ->when(request()->chapter, function ($query, $chapter_id) {
                $query->where('chapter_id', $chapter_id);
            })
            ->when(request()->topic, function ($query, $topic_id) {
                $query->where('topic_id', $topic_id);
            })
            ->latest('id')
            ->paginate(20);
    }

    protected function getSelectedQuestionId($quiz, $question_type)
    {
        return $quiz
            ->quiz_questions()
            ->where('question_type', $question_type)
            ->pluck('question_id')
            ->toArray();
    }

    public function assign(Request $request, Quiz $quiz, $question_type)
    {
        if(QuizParticipant::where('quiz_id', $quiz->id)->exists()) {
            return abort(404);
        }

        $property_number_of_question = $quiz->quiz_property
            ->quiz_property_items
            ->where('question_type', $question_type)
            ->first()
            ->number_of_question ?? 0;

        $assign_serial = $quiz->quiz_questions()
            ->where('question_type', $question_type)
            ->pluck('serial')
            ->toArray();

        $serial = 0;

        for($i = 1; $i <= $property_number_of_question; $i++) {
            if(!in_array($i, $assign_serial)) {
                $serial = $i;
                break;
            }
        }

        $question = Question::query()
            ->where('type', $question_type)
            ->find($request->question_id);

        $allow = true;

        if($request->checked) {
            $assign_number_of_question = $quiz->quiz_questions()
                ->where('question_type', $question_type)
                ->count();

            $allow = $property_number_of_question > $assign_number_of_question;
        }

        if($question && $allow) {
            $options = Array();

            $question->question_options->map(function ($option) use (&$options) {
                $options[] = [
                    "title"     => $option->title,
                    "serial"    => $option->serial,
                ];
            });

            $quiz_question = QuizQuestion::withTrashed()->updateOrCreate(
                [
                    'quiz_id'       => $quiz->id,
                    'question_id'   => $question->id,
                ],
                [
                    'question_type'     => $question->type,
                    'question_title'    => $question->title ?? '',
                    'question_options'  => json_encode($options ?? []),
                    'answer_script'     => $question->answer_script ?? '',
                    'serial'            => $serial,
                    'deleted_at'        => $request->checked ? NULL : now(),
                ]
            );
        }

        if(request()->redirect == 'back') {
            return back();
        }

        return response([
            'question_id'   => $request->question_id,
            'message'       => $request->checked ? ($allow ? 'Added!' : 'Question Full') : 'Removed!',
            'totalQuestion' => $quiz->quiz_questions()->where('question_type', $question_type)->count(),
            'success'       => $allow,
        ], 200);
    }

    public function result()
    {
        $filter_quiz_id = request()->quiz ?? '';
        $search = request()->search ?? '';

        $quizzes = Quiz::query()
            ->whereHas('quiz_participants')
            ->latest()
            ->get();
            
        if(request()->isXmlHttpRequest()) {
            $participants = QuizParticipant::query()
                ->with('doctor', 'quiz.quiz_property')
                ->whereHas('doctor')
                ->whereHas('quiz')
                ->when($filter_quiz_id, function ($query, $quiz_id) {
                    $query->where('quiz_id', $quiz_id);
                })
                ->when($search, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query
                            ->whereHas('quiz', function ($query) use ($search) {
                                $query->where('title', 'like', "%{$search}%")
                                    ->orWhere('coupon', 'like', "%{$search}%");
                            })
                            ->orWhereHas('doctor', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%")
                                    ->orWhere('bmdc_no', 'like', "%{$search}%")
                                    ->orWhere('mobile_number', 'like', "%{$search}%");
                            });
                    });
                })
                ->orderBy('obtained_mark', 'desc')
                ->paginate();

            return  view('tailwind.admin.quiz-results.data', compact('participants', 'search'))->render();
        }

        return view('tailwind.admin.quiz-results.index', compact('quizzes', 'filter_quiz_id', 'search'));
    }
}
