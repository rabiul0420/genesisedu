<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Sms;
use App\Question;
use App\QuestionChapter;
use App\QuestionReference;
use App\QuestionReferenceExam;
use App\Questions;
use App\QuestionTopic;
use App\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class QuestionPrintController extends Controller
{
    public function question_print(Request $request)
    {

        $question_code = null;

        $admin = Auth::user();

        $date = Carbon::now();

        $data = Setting::property('question_print_allow')->first();

        if (!(is_array(json_decode($data->value)) == Auth::user()->id)) {
            return abort(404);
        }

        if ($request->chapter && $request->topic) {
            $questions = Question::query()
                ->where(['chapter_id' => $request->chapter, 'topic_id' => $request->topic, 'type' => $request->type])
                ->with('chapter', 'topic')
                ->get(['id', 'question_and_answers', 'reference', 'discussion', 'chapter_id', 'topic_id']);

            $chapter = QuestionChapter::query()
                ->where('id', $request->chapter)
                ->first();

            $topic = QuestionTopic::query()
                ->where('id', $request->topic)
                ->first();

            $message = "{$admin->name} print {$request->question_type} Question of {$topic->topic_name}  from {$chapter->chapter_name} chapter at {$date->toRfc850String()}";

            $this->sendSmsForQuestionPrint($message);

            return view('tailwind.admin.question-source.question-source-print', compact('questions', 'chapter', 'topic', 'question_code'));
        }

        $questionIds = QuestionReference::where('reference_id', $request->reference)->pluck('question_id');

        $questions = Question::query()
            ->whereIn('id', $questionIds)
            ->where('type', $request->type)
            ->get(['id', 'question_and_answers', 'reference', 'discussion']);

        $question_code = QuestionReferenceExam::where('id', $request->reference)->first();

        $message = "{$admin->name} Print {$request->question_type} Question of {$question_code->reference_code} Question Source At {$date->toRfc850String()}";

        $this->sendSmsForQuestionPrint($message);

        return view('tailwind.admin.question-source.question-source-print', compact('questions', 'question_code'));
    }

    public function sendSmsForQuestionPrint($message)
    {

        $question_print_sms_to = Setting::property('question_print_sms_to')->value('value');

        foreach (json_decode($question_print_sms_to) as $number) {
            $this->sendSmsWithQuestionPrintLog($number, $message);
        }
    }

    public function sendSmsWithQuestionPrintLog($number, $text)
    {
        $event = "Question Print";

        $sms = Sms::init()->setRecipient($number)->setText($text);

        $sms->send();

        $sms->save_log($event, $user = 0, $number, Auth::id());
    }
}
